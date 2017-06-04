<?php
declare(strict_types=1);

namespace AppBundle\Action\Image;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Response;
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
     *     path="/images-enrich"
     * )
     * @Method("GET")
     *
     * @return mixed
     */
    public function __invoke()
    {
        $em = $this->entityManager;

        $imageCount = $em->createQueryBuilder()
            ->select('count(i.id)')
            ->from('AppBundle\Entity\Image', 'i')
            ->getQuery()
            ->getSingleScalarResult();

        $randomImage = $em->createQueryBuilder()
            ->select('i')
            ->from('AppBundle\Entity\Image', 'i')
            ->setMaxResults(1)
            ->setFirstResult(rand(1, intval($imageCount)))
            ->getQuery()
            ->getResult();

        $metadataCount = $em->createQueryBuilder()
            ->select('count(m)')
            ->from('AppBundle\Entity\Metadata', 'm')
            ->getQuery()
            ->getSingleScalarResult();

        $randomMetadata = $em->createQueryBuilder()
            ->select('m')
            ->from('AppBundle\Entity\Metadata', 'm')
            ->setMaxResults(1)
            ->setFirstResult(rand(1, intval($metadataCount)))
            ->getQuery()
            ->getResult();

        return new Response($this->serializer->encode([
            'image' => $this->serializer->encode($randomImage, 'json-ld'),
            'metadata' => $this->serializer->encode($randomMetadata, 'json-ld'),
            'question' => 'The big question?'
        ], 'json'));
    }
}