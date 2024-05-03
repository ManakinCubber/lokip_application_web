<?php

namespace App\Repository;

use App\Entity\Computer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Computer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Computer|null findOneBy(array $criteria, array $orderBy = null)
 * @method Computer[]    findAll()
 * @method Computer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComputerRepository extends ServiceEntityRepository
{
    private $authorizedCountryRepository;

    public function __construct(ManagerRegistry $registry, AuthorizedCountryRepository $authorizedCountryRepository)
    {
        parent::__construct($registry, Computer::class);
        $this->authorizedCountryRepository = $authorizedCountryRepository;
    }

    public function getTotalComputers(): int
    {
        return $this->createQueryBuilder('c')
            ->select('count(c.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }
}
