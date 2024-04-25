<?php

namespace App\Repository;

use App\Entity\Livreaudio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Livreaudio>
 *
 * @method Livreaudio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Livreaudio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Livreaudio[]    findAll()
 * @method Livreaudio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LivreaudioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livreaudio::class);
    }

//    /**
//     * @return Livreaudio[] Returns an array of Livreaudio objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Livreaudio
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
