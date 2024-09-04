<?php

namespace App\Repository;

use App\Entity\CategorieVod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategorieVod>
 *
 * @method CategorieVod|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieVod|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieVod[]    findAll()
 * @method CategorieVod[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieVodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieVod::class);
    }

//    /**
//     * @return CategorieVod[] Returns an array of CategorieVod objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?CategorieVod
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
