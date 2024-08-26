<?php

namespace App\Repository;

use App\Entity\ResultatQuestionnaire;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ResultatQuestionnaire>
 *
 * @method ResultatQuestionnaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method ResultatQuestionnaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method ResultatQuestionnaire[]    findAll()
 * @method ResultatQuestionnaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResultatQuestionnaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ResultatQuestionnaire::class);
    }

//    /**
//     * @return ResultatQuestionnaire[] Returns an array of ResultatQuestionnaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('r.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?ResultatQuestionnaire
//    {
//        return $this->createQueryBuilder('r')
//            ->andWhere('r.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
