<?php

namespace App\Repository;

use App\Entity\AddressIp;
use App\Entity\AuthorizedCountry;
use App\Entity\Computer;
use App\Entity\ComputerAuthorizedCountry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\VerifiedAlert;
use Exception;
use Psr\Log\LoggerInterface;

class AddressIpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AddressIp::class);
    }

    public function findAllCountries(): array
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('ac.countryName')
            ->leftJoin('a.country', 'ac')
            ->distinct();

        $query = $queryBuilder->getQuery();

        $countries = array_column($query->getResult(), 'countryName');

        return $countries;
    }


    public function findByCountries(array $countries)
    {
        $em = $this->getEntityManager();

        $countryIds = $em->getRepository(AuthorizedCountry::class)
            ->createQueryBuilder('ac')
            ->select('ac.id')
            ->where('ac.countryName IN (:countries)')
            ->setParameter('countries', $countries)
            ->getQuery()
            ->getScalarResult();

        $countryIds = array_column($countryIds, 'id');

        return $this->createQueryBuilder('a')
            ->leftJoin('a.computer', 'c')
            ->addSelect('c')
            ->where('a.country IN (:countryIds)')
            ->setParameter('countryIds', $countryIds)
            ->orderBy('a.collectionDateTime', 'desc')
            ->getQuery()
            ->getResult();
    }

    public function getCountryRanking(): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM get_country_ranking()';
        $stmt = $conn->executeQuery($sql);

        $result = $stmt->fetchAll();

        return $result;
    }
    public function setVerifiedForComputer(int $computerId): int
    {
        $em = $this->getEntityManager();

        $computer = $em->getRepository(Computer::class)->find($computerId);

        $country = $computer->getAddressIps()->first()->getCountry();

        $query = $em->createQuery(
            'UPDATE App\Entity\AddressIp a
        SET a.verified = true
        WHERE a.computer = :computer AND a.country = :country'
        )->setParameters([
            'computer' => $computer,
            'country' => $country,
        ]);

        $updatedCount = $query->execute();

        $verifiedAlert = new VerifiedAlert();
        $verifiedAlert->setAddressIp($computer->getAddressIps()->first());

        $em->persist($verifiedAlert);
        $em->flush();

        return $updatedCount;
    }

    public function setAllVerifiedForComputer(int $computerId): int
    {
        $em = $this->getEntityManager();

        $computer = $em->getRepository(Computer::class)->find($computerId);

        $query = $em->createQuery(
            'UPDATE App\Entity\AddressIp a
        SET a.verified = true
        WHERE a.computer = :computer AND a.verified = false'
        )->setParameter('computer', $computer);

        $updatedCount = $query->execute();

        foreach ($computer->getAddressIps() as $addressIp) {
            if (!$addressIp->getVerified()) {
                $verifiedAlert = new VerifiedAlert();
                $verifiedAlert->setAddressIp($addressIp);

                $em->persist($verifiedAlert);
            }
        }

        $em->flush();

        return $updatedCount;
    }

    /**
     * @throws Exception
     */
    public function setAuthorizeForComputer(int $id): void
    {
        $entityManager = $this->getEntityManager();

        $addressIp = $entityManager->getRepository(AddressIp::class)->findOneBy(['id' => $id]);

        if (!$addressIp) {
            throw new \InvalidArgumentException("L'adresse IP avec l'ID $id n'existe pas.");
        }

        $computerAuthorizedCountry = new ComputerAuthorizedCountry();
        $computerAuthorizedCountry->setComputer($addressIp->getComputer());
        $computerAuthorizedCountry->setCountry($addressIp->getCountry());

        $entityManager->persist($computerAuthorizedCountry);
        $entityManager->flush();
    }


    public function setAllAuthorizeForComputer(int $computerId): bool
    {
        $entityManager = $this->getEntityManager();

        $computer = $entityManager->getRepository(Computer::class)->find($computerId);

        if (!$computer) {
            throw new \InvalidArgumentException("L'ordinateur avec l'ID $computerId n'existe pas.");
        }

        $computer->setAllLocationsAllow(true);
        $entityManager->persist($computer);
        $entityManager->flush();

        if ($computer->getAllLocationsAllow() === true) {
            return true;
        } else {
            return false;
        }
    }

    public function generateCsvData($hostnames, $startDate, $endDate, $countries, LoggerInterface $logger)
    {
        $logger->info('Start date: ' . $startDate);
        $logger->info('End date: ' . $endDate);

        $conn = $this->getEntityManager()->getConnection();

        $sql = 'SELECT * FROM public.generate_csv_data(:hostnames, :startDate, :endDate, :countries)';
        $stmt = $conn->prepare($sql);

        if ($countries === null || empty($countries)) {
            $countries = null;
        } else {
            $countries = '{"' . implode('", "', $countries) . '"}';
        }

        $stmt->execute([
            'hostnames' => $hostnames,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'countries' => $countries,
        ]);

        $csvData = [];
        $result = $stmt->executeQuery();
        while ($row = $result->fetchAssociative()) {
            $csvData[] = $row;
        }

        return $csvData;
    }



}
