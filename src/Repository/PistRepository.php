<?php

namespace App\Repository;

use App\Entity\Pist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pist[]    findAll()
 * @method Pist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pist::class);
    }

    // /**
    //  * @return Pist[] Returns an array of Pist objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pist
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
