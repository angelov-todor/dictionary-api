<?php
declare(strict_types=1);

namespace AppBundle\Services\Word;

class DoctrineAdapter implements AdapterInterface
{
    protected $entityManager;

    protected $entityClassname;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $entityClassname
     * @return DoctrineAdapter
     */
    public function setEntityClassname(string $entityClassname)
    {
        $this->entityClassname = $entityClassname;
        return $this;
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    public function setEntityManager(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        return $this;
    }

    public function findWord($word)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('d')->from($this->entityClassname, 'd')
            ->where('d.normalized like :word')
            ->setParameter('word', $word);

        $query = $qb->getQuery();
        $found = $query->getOneOrNullResult();
//        if ($found instanceof \Application\Entity\Dictionary) {
        if ($found instanceof $this->entityClassname) {
            return $found->getArrayCopy();
        }

        return $found;
    }
}