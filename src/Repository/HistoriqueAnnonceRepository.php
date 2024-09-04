<?php

namespace App\Repository;

use App\Entity\HistoriqueAnnonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HistoriqueAnnonce>
 *
 * @method HistoriqueAnnonce|null find($id, $lockMode = null, $lockVersion = null)
 * @method HistoriqueAnnonce|null findOneBy(array $criteria, array $orderBy = null)
 * @method HistoriqueAnnonce[]    findAll()
 * @method HistoriqueAnnonce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HistoriqueAnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HistoriqueAnnonce::class);
    }

//    /**
//     * @return HistoriqueAnnonce[] Returns an array of HistoriqueAnnonce objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('h.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?HistoriqueAnnonce
//    {
//        return $this->createQueryBuilder('h')
//            ->andWhere('h.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
