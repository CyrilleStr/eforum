<?php

namespace App\Repository;

use App\Entity\PostView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostView|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostView|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostView[]    findAll()
 * @method PostView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostViewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostView::class);
    }

    // /**
    //  * @return PostView[] Returns an array of PostView objects
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
    public function findOneBySomeField($value): ?PostView
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
