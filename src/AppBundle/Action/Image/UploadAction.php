<?php
declare(strict_types=1);

namespace AppBundle\Action\Image;

use AppBundle\Entity\Image;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RequestContext;

class UploadAction
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return CacheManager
     */
    protected function getCacheManager()
    {
        return $this->container->get('liip_imagine.cache.manager');
    }

    /**
     * @return DataManager
     */
    protected function getDataManager()
    {
        return $this->container->get('liip_imagine.data.manager');
    }

    /**
     * @return FilterManager
     */
    protected function getFilterManager()
    {
        return $this->container->get('liip_imagine.filter.manager');
    }

    /**
     * @Route(
     *     name="media",
     *     path="/assets/{resource}"
     * )
     * @Method("GET")
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     */
    public function serveAction(Request $request)
    {
        $image = $request->get('resource');

        $path = getcwd() . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $image;
        if (!file_exists($path)) {
            return new JsonResponse([
                'location' => $path,
                'cwd' => getcwd(),
                'error' => 'Not found'
            ], 404);
        }

        $filter = 'my_thumb';

        if (!$this->getCacheManager()->isStored($path, $filter, null)) {
            try {
                $binary = $this->getDataManager()->find($filter, $path);
                $this->getCacheManager()->store(
                    $this->getFilterManager()->applyFilter($binary, $filter),
                    $path,
                    $filter,
                    null
                );
            } catch (NotLoadableException $e) {
                if ($defaultImageUrl = $this->getDataManager()->getDefaultImageUrl($filter)) {
                    $path = $defaultImageUrl;
                } else {
                    throw new NotFoundHttpException('Source image could not be found', $e);
                }
            }
        }
//        $this->getBaseUrl($request);
        $resolved = $this->getCacheManager()->resolve($path, $filter, null);

        return new RedirectResponse($resolved, 301);
    }

    /**
     * @Route(
     *     name="images_upload",
     *     path="/images-upload"
     * )
     * @Method("POST")
     *
     * @param Request $request
     * @return JsonResponse|Response|Image
     */
    public function imageUploadAction(Request $request)
    {
        $requestContent = $request->getContent();
        $json = json_decode($requestContent);

        $filename = $json->filename;
        $data = $json->data;

        if (!$filename || !$data) {
            return new JsonResponse([
                'filename' => $filename,
                'data' => $data,
                'content' => $request->getContent()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $location = 'assets' . DIRECTORY_SEPARATOR . $this->getToken(40) . '.' . $ext;
        $file = getcwd() . DIRECTORY_SEPARATOR . $location;

        $image = new Image();
        $this->base64ToJpeg($data, $file);
        $image->setSrc($location);

        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $entityManager->persist($image);
        $entityManager->flush($image);

        return new JsonResponse($image, Response::HTTP_CREATED);
    }

    /**
     * @param RequestContext $requestContext
     * @return string
     */
    protected function getBaseUrl(Request $requestContext)
    {
        $port = '';
        if ('https' == $requestContext->getScheme() && $requestContext->getHttpsPort() != 443) {
            $port = ":{$requestContext->getHttpsPort()}";
        }

        if ('http' == $requestContext->getScheme() && $requestContext->getHttpPort() != 80) {
            $port = ":{$requestContext->getHttpPort()}";
        }

        $baseUrl = $requestContext->getBaseUrl();
        if ('.php' == substr($requestContext->getBaseUrl(), -4)) {
            $baseUrl = pathinfo($requestContext->getBaseurl(), PATHINFO_DIRNAME);
        }
        $baseUrl = rtrim($baseUrl, '/\\');

        return sprintf('%s://%s%s%s',
            $requestContext->getScheme(),
            $requestContext->getHost(),
            $port,
            $baseUrl
        );
    }

    /**
     * @param string $base64
     * @param string $filename
     * @return string
     */
    protected function base64ToJpeg(string $base64, string $filename): string
    {
        // open the output file for writing
        $ifp = fopen($filename, 'wb');

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode(',', $base64);

        // we could add validation here with ensuring count( $data ) > 1
        fwrite($ifp, base64_decode($data[1]));

        // clean up the file resource
        fclose($ifp);

        return $filename;
    }

    /**
     * @param int $length
     * @return string
     */
    protected function getToken(int $length): string
    {
        $token = "";
        $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
        $codeAlphabet .= "0123456789";
        $max = strlen($codeAlphabet); // edited

        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[rand(0, $max - 1)];
        }

        return $token;
    }
}