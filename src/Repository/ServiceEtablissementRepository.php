<?php

namespace App\Repository;

use App\Entity\ServiceEtablissement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ServiceEtablissement>
 *
 * @method ServiceEtablissement|null find($id, $lockMode = null, $lockVersion = null)
 * @method ServiceEtablissement|null findOneBy(array $criteria, array $orderBy = null)
 * @method ServiceEtablissement[]    findAll()
 * @method ServiceEtablissement[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ServiceEtablissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ServiceEtablissement::class);
    }

//    /**
//     * @return ServiceEtablissement[] Returns an array of ServiceEtablissement objects
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

//    public function findOneBySomeField($value): ?ServiceEtablissement
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
