<?php

namespace App\Repository;

use App\Entity\TypeServiceEnChambre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TypeServiceEnChambre>
 *
 * @method TypeServiceEnChambre|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeServiceEnChambre|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeServiceEnChambre[]    findAll()
 * @method TypeServiceEnChambre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeServiceEnChambreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeServiceEnChambre::class);
    }

//    /**
//     * @return TypeServiceEnChambre[] Returns an array of TypeServiceEnChambre objects
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

//    public function findOneBySomeField($value): ?TypeServiceEnChambre
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
