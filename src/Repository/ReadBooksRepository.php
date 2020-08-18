<?php

namespace App\Repository;

use App\Entity\ReadBooks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReadBooks|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReadBooks|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReadBooks[]    findAll()
 * @method ReadBooks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReadBooksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReadBooks::class);
    }

    // /**
    //  * @return ReadBooks[] Returns an array of ReadBooks objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ReadBooks
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
