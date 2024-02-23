<?php

namespace App\Repository;

use App\Entity\Television;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Television>
 *
 * @method Television|null find($id, $lockMode = null, $lockVersion = null)
 * @method Television|null findOneBy(array $criteria, array $orderBy = null)
 * @method Television[]    findAll()
 * @method Television[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TelevisionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Television::class);
    }

//    /**
//     * @return Television[] Returns an array of Television objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Television
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
