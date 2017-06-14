<?php
declare(strict_types=1);

namespace AppBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use AppBundle\Entity\Image;
use AppBundle\Entity\ImageMetadata;
use AppBundle\Entity\Metadata;
use AppBundle\Services\GoogleVisionService;
use AppBundle\Services\Word;
use AppBundle\Services\WordTools;
use Doctrine\ORM\EntityManager;
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
     * ImageMetadataSubscriber constructor.
     * @param EntityManager $entityManager
     * @param WordTools $wordTools
     */
    public function __construct(EntityManager $entityManager, WordTools $wordTools)
    {
        $this->entityManager = $entityManager;
        $this->wordTools = $wordTools;
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

        $metas = [
            'Фонеми' => 'phonemes',
            'Сричкоделение' => 'syllables',
            'Римоформа' => 'rhymeform',
            'Транскрипция' => 'transcription'
        ];

        foreach ($metas as $meta => $tool) {
            $imageMetadata = new ImageMetadata();
            $imageMetadata->setImage($imageMetadata->getImage());
            try {
                $metadata = $this->findMetadataByName($meta);
            } catch (\Exception $e) {
                continue;
            }

            $imageMetadata->setMetadata($metadata);
            $imageMetadata->setValue(
                $this->getValueFromTool($tool, $imageMetadata->getValue())
            );

            $this->getEntityManager()->persist($imageMetadata);
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