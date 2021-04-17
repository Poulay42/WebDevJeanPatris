<?php

namespace App\Repository;

use App\Entity\Cosplay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Cosplay|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cosplay|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cosplay[]    findAll()
 * @method Cosplay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CosplayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cosplay::class);
    }

    // /**
    //  * @return Cosplay[] Returns an array of Cosplay objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Cosplay
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
