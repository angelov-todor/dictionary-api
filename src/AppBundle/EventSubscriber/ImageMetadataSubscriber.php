<?php
declare(strict_types=1);

namespace AppBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageMetadata;
use AppBundle\Entity\Metadata;
use AppBundle\Services\GoogleVisionService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ImageMetadataSubscriber implements EventSubscriberInterface
{
    /**
     * @var GoogleVisionService
     */
    protected $visionService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * ImageMetadataSubscriber constructor.
     * @param GoogleVisionService $visionService
     * @param EntityManager $entityManager
     */
    public function __construct(GoogleVisionService $visionService, EntityManager $entityManager)
    {
        $this->visionService = $visionService;
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [['generateMetadata', EventPriorities::POST_WRITE]],
        ];
    }

    /**
     * @return GoogleVisionService
     */
    protected function getVisionService(): GoogleVisionService
    {
        return $this->visionService;
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    public function generateMetadata(GetResponseForControllerResultEvent $event):?JsonResponse
    {
        $image = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$image instanceof Image || Request::METHOD_POST !== $method) {
            return null;
        }

        $file = getcwd() . DIRECTORY_SEPARATOR . Image::IMAGE_LOCATION . DIRECTORY_SEPARATOR . $image->getSrc();

        try {
            $metadata = $this->findMetadataByName('label');
        } catch (\Exception $e) {
            return null;
        }

        $labelDetection = $this->getVisionService()->execute($file);
        foreach ($labelDetection as $entity) {
            $imageMetadata = new ImageMetadata();
            $imageMetadata->setImage($image);
            $imageMetadata->setMetadata($metadata);
            $imageMetadata->setValue($entity->description());
            $this->getEntityManager()->persist($imageMetadata);
            $this->getEntityManager()->flush($imageMetadata);
        }

        return new JsonResponse(['@id' => 'images/' . $image->getId()]);
    }

    /**
     * @param string $name
     * @return Metadata
     * @throws \Exception
     */
    protected function findMetadataByName(string $name): Metadata
    {
        $meta = $this->getEntityManager()
            ->getRepository(Metadata::class)
            ->findOneBy(['name' => $name]);
        if (null == $meta) {
            throw new \Exception("No metadata with name $name");
        }
        return $meta;
    }
}