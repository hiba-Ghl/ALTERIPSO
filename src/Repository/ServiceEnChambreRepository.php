<?php

namespace App\Repository;

use App\Entity\ServiceEnChambre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceEnChambre>
 *
 * @method ServiceEnChambre|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceEnChambre|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceEnChambre[]    findAll()
 * @method ServiceEnChambre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceEnChambreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceEnChambre::class);
    }

//    /**
//     * @return ServiceEnChambre[] Returns an array of ServiceEnChambre objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ServiceEnChambre
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
