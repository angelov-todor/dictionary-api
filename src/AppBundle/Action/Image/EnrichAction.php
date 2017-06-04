<?php
declare(strict_types=1);

namespace AppBundle\Action\Image;

use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EnrichAction
{
    /**
     * @var ContainerInterface
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route(
     *     name="enrich",
     *     path="/images-enrich"
     * )
     * @Method("GET")
     *
     * @param Request $request
     * @return mixed
     */
    public function enrichAction(Request $request)
    {
        $em = $this->entityManager;
        $qb = $em->createQueryBuilder();

        $imageCount = $em->createQueryBuilder()
            ->select('count(i.id)')
            ->from('AppBundle\Entity\Image', 'i')
            ->getQuery()
            ->getSingleScalarResult();


        $randomImage = $em->createQueryBuilder()
            ->select('i')
            ->from('AppBundle\Entity\Image', 'i')
            ->setMaxResults(1)
            ->setFirstResult(rand(1, $imageCount))
            ->getQuery()->getResult();

        $metadataCount = $em->createQueryBuilder()
            ->select('count(m)')
            ->from('AppBundle\Entity\Metadata', 'm')
            ->getQuery()
            ->getSingleScalarResult();

        $randomMetadata = $em->createQueryBuilder()
            ->select('m')
            ->from('AppBundle\Entity\Metadata', 'm')
            ->setMaxResults(1)
            ->setFirstResult(rand(1, $metadataCount))
            ->getQuery()
            ->getResult();

        $obj = new \stdClass();
        $obj->image = $randomImage;
        $obj->metadata = $randomMetadata;
        $obj->question = 'The big question?';

        return $obj;
    }
}