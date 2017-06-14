<?php
declare(strict_types=1);

namespace AppBundle\Action\Image;

use AppBundle\Entity\Image;
use AppBundle\Entity\ImageUpload;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UploadAction
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
     * @Route(
     *     name="images_upload",
     *     path="/images-upload",
     *     defaults={"_api_resource_class"=ImageUpload::class, "_api_collection_operation_name"="images_upload"}
     * )
     * @Method("POST")
     * @param mixed $data
     * @return JsonResponse|Response|Image
     */
    public function __invoke(ImageUpload $data)
    {
        $filename = $data->getFilename();
        $fileData = $data->getData();

        if (!$filename || !$fileData) {
            return new JsonResponse([
                'filename' => $filename,
                'data' => $fileData
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $uuidName = $this->getToken(40) . '.' . $ext;
        $location = Image::IMAGE_LOCATION . DIRECTORY_SEPARATOR . $uuidName;
        $file = getcwd() . DIRECTORY_SEPARATOR . $location;

        $image = new Image();
        $this->base64ToJpeg($fileData, $file);
        $image->setSrc($uuidName);

        $entityManager = $this->container->get('doctrine.orm.entity_manager');
        $entityManager->persist($image);
        $entityManager->flush($image);
        return $image;
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