<?php

namespace App\Repository;

use App\Entity\ComputerAuthorizedCountry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ComputerAuthorizedCountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ComputerAuthorizedCountry::class);
    }

    public function findByComputerId(int $computerId): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.computer = :computerId')
            ->setParameter('computerId', $computerId)
            ->getQuery()
            ->getResult();
    }
}