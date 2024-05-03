<?php

namespace App\Repository;

use App\Entity\AuthorizedCountry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AuthorizedCountry|null find($id, $lockMode = null, $lockVersion = null)
 * @method AuthorizedCountry|null findOneBy(array $criteria, array $orderBy = null)
 * @method AuthorizedCountry[]    findAll()
 * @method AuthorizedCountry[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorizedCountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AuthorizedCountry::class);
    }

    public function findIdByCountryName(string $countryName): ?int
    {
        $country = $this->findOneBy(['countryName' => $countryName]);

        if ($country) {
            return $country->getId();
        }
        return null;
    }

    public function findIdsByNames(array $names): array
    {
        $queryBuilder = $this->createQueryBuilder('ac')
            ->select('ac.id')
            ->where('ac.countryName IN (:names)')
            ->setParameter('names', $names);

        $query = $queryBuilder->getQuery();

        $ids = array_column($query->getResult(), 'id');

        if (empty($ids)) {
            return [];
        }

        return $ids;
    }
}