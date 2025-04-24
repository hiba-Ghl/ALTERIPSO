<?php

namespace App\Repository;

use App\Entity\LancerTV;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends TVEntityRepository<LancerTV>
 */
class LancerTVRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LancerTV::class);
    }

}