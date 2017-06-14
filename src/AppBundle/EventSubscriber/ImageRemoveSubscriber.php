<?php
declare(strict_types=1);

namespace AppBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use AppBundle\Entity\Image;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ImageRemoveSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * ImageRemoveSubscriber constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['removeImageMetadata', EventPriorities::PRE_WRITE],
        ];
    }

    /**
     * @return EntityManager
     */
    protected function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    public function removeImageMetadata(GetResponseForControllerResultEvent $event): void
    {
        $image = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$image instanceof Image || Request::METHOD_DELETE !== $method) {
            return;
        }
        foreach ($image->getImageMetadata() as $imageMetadatum) {
            $this->getEntityManager()->remove($imageMetadatum);
        }
    }
}