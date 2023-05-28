<?php

namespace App\Repository;

use App\Entity\ReservationChange;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ReservationChange|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReservationChange|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReservationChange[]    findAll()
 * @method ReservationChange[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationChangeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ReservationChange::class);
    }

    // /**
    //  * @return ReservationChange[] Returns an array of ReservationChange objects
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
    public function findOneBySomeField($value): ?ReservationChange
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
