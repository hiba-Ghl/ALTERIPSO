<?php

namespace App\Repository;

use App\Entity\LancerAnnonce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AnnonceEntityRepository<LancerAnnonce>
 */
class LancerAnnonceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LancerAnnonce::class);
    }

}
