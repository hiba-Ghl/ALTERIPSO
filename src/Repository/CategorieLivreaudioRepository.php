<?php

namespace App\Repository;

use App\Entity\CategorieLivreaudio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CategorieLivreaudio>
 *
 * @method CategorieLivreaudio|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategorieLivreaudio|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategorieLivreaudio[]    findAll()
 * @method CategorieLivreaudio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategorieLivreaudioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CategorieLivreaudio::class);
    }

//    /**
//     * @return CategorieLivreaudio[] Returns an array of CategorieLivreaudio objects
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

//    public function findOneBySomeField($value): ?CategorieLivreaudio
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
