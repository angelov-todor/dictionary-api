<?php
declare(strict_types=1);

namespace AppBundle\Action\Image;

use AppBundle\Entity\Enrichment;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

class EnrichAction
{
    /**
     * @var EntityManager
     */
    protected $entityManager;
    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * EnrichAction constructor.
     * @param EntityManager $entityManager
     * @param Serializer $serializer
     */
    public function __construct(EntityManager $entityManager, Serializer $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * @Route(
     *     name="enrich",
     *     path="/images-enrich",
     *     defaults={"_api_resource_class"=Enrichment::class, "_api_collection_operation_name"="enrich"}
     * )
     * @Method("GET")
     *
     * @return mixed
     */
    public function __invoke()
    {
        $imageCount = $this->entityManager->createQueryBuilder()
            ->select('count(i.id)')
            ->from('AppBundle\Entity\Image', 'i')
            ->getQuery()
            ->getSingleScalarResult();

        $randomImage = $this->entityManager->createQueryBuilder()
            ->select('i')
            ->from('AppBundle\Entity\Image', 'i')
            ->setMaxResults(1)
            ->setFirstResult(rand(0, intval($imageCount) - 1))
            ->getQuery()
            ->getSingleResult();

        $metadataCount = $this->entityManager->createQueryBuilder()
            ->select('count(m)')
            ->from('AppBundle\Entity\Metadata', 'm')
            ->getQuery()
            ->getSingleScalarResult();

        $randomMetadata = $this->entityManager->createQueryBuilder()
            ->select('m')
            ->from('AppBundle\Entity\Metadata', 'm')
            ->setMaxResults(1)
            ->setFirstResult(rand(0, intval($metadataCount) - 1))
            ->getQuery()
            ->getSingleResult();

        $q = 'The big question?';

        return new Enrichment(
            $randomImage,
            $randomMetadata,
            $q
        );
    }
}