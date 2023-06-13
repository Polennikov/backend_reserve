<?php

namespace App\Repository;

use App\Entity\Plan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Plan|null find($id, $lockMode = null, $lockVersion = null)
 * @method Plan|null findOneBy(array $criteria, array $orderBy = null)
 * @method Plan[]    findAll()
 * @method Plan[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plan::class);
    }

    /**
     * @param int $manager
     * @return array<string>
     */
   /* public function findAllByNumber(int $manager): array
    {
        $entityManager = $this->getEntityManager();
        $query = 'SELECT full_name FROM Manager WHERE id = ' . $manager . ';';
        $statement = $entityManager->getConnection()->executeQuery($query);
        return $statement->fetch();
    }*/


    /*
    public function findOneBySomeField($value): ?Manager
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
