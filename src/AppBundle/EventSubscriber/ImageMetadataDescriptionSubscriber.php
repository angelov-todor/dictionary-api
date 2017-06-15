<?php
declare(strict_types=1);

namespace AppBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use AppBundle\Entity\ImageMetadata;
use AppBundle\Entity\Metadata;
use AppBundle\Services\WordTools;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class ImageMetadataDescriptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var WordTools
     */
    protected $wordTools;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * ImageMetadataSubscriber constructor.
     * @param EntityManager $entityManager
     * @param WordTools $wordTools
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $entityManager, WordTools $wordTools, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->wordTools = $wordTools;
        $this->logger = $logger;
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
     * @return EntityManager
     */
    protected function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    public function generateMetadata(GetResponseForControllerResultEvent $event):?JsonResponse
    {
        $imageMetadata = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$imageMetadata instanceof ImageMetadata || Request::METHOD_POST !== $method) {
            return null;
        }
        if ($imageMetadata->getMetadata()->getName() != 'Description'
            || $imageMetadata->getMetadata()->getName() != 'Описание'
        ) {
            return null;
        }
        $this->logger->debug('starting.');

        $metas = [
            'Фонеми' => 'phonemes',
            'Сричкоделение' => 'syllables',
            'Римоформа' => 'rhymeform',
            'Транскрипция' => 'transcription'
        ];

        foreach ($metas as $meta => $tool) {
            $imageMetadataO = new ImageMetadata();
            $imageMetadataO->setImage($imageMetadata->getImage());
            try {
                $metadata = $this->findMetadataByName($meta);
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
                continue;
            }

            $imageMetadataO->setMetadata($metadata);
            try {
                $imageMetadataO->setValue(
                    $this->getValueFromTool($tool, $imageMetadata->getValue())
                );
                $this->getEntityManager()->persist($imageMetadataO);
                $this->getEntityManager()->flush($imageMetadataO);
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
                continue;
            }
        }

        return null;
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

    /**
     * @return WordTools
     */
    protected function getWordTools(): WordTools
    {
        return $this->wordTools;
    }

    /**
     * @param string $tool
     * @param string $word
     * @return string
     */
    protected function getValueFromTool($tool, $word)
    {
        return $this->getWordTools()->{$tool}($word);
    }
}