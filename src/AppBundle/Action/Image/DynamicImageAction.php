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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class DynamicImageAction
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * UploadAction constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return CacheManager
     */
    protected function getCacheManager(): CacheManager
    {
        /** @var CacheManager $cacheManager */
        $cacheManager = $this->container->get('liip_imagine.cache.manager');
        return $cacheManager;
    }

    /**
     * @return DataManager
     */
    protected function getDataManager(): DataManager
    {
        /** @var DataManager $dataManager */
        $dataManager = $this->container->get('liip_imagine.data.manager');
        return $dataManager;
    }

    /**
     * @return FilterManager
     */
    protected function getFilterManager(): FilterManager
    {
        /** @var FilterManager $filterManager */
        $filterManager = $this->container->get('liip_imagine.filter.manager');
        return $filterManager;
    }

    /**
     * @Route(
     *     name="dynamic-image",
     *     path="/image/{filter}/{resource}"
     * )
     * @Method("GET")
     *
     * @param Request $request
     * @return RedirectResponse|JsonResponse|Response
     */
    public function dynamicImage(Request $request): Response
    {
        $image = $request->get('resource');

        $path = Image::IMAGE_LOCATION . DIRECTORY_SEPARATOR . $image;
        if (!file_exists($path)) {
            return new JsonResponse([
                'location' => $path,
                'cwd' => getcwd(),
                'error' => 'Not found'
            ], 404);
        }

        $filter = $request->get('filter');

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
        $resolved = $this->getCacheManager()->resolve($path, $filter, null);
        return new RedirectResponse($resolved, 301);
    }
}