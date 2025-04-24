<?php

namespace App\Repository;

use App\Entity\LancerRadio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends TVEntityRepository<LancerTV>
 */
class LancerRadioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LancerRadio::class);
    }

}