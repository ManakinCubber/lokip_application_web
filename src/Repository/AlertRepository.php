<?php

namespace App\Repository;

use App\Entity\Alert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Alert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Alert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Alert[]    findAll()
 * @method Alert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AlertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Alert::class);
    }

    public function findAllAlerts()
    {
        return $this->createQueryBuilder('a')
            ->getQuery()
            ->getResult();
    }


    public function countByLevelAlertZero()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.levelAlert = :val')
            ->setParameter('val', 0)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByLevelAlertOne()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.levelAlert = :val')
            ->setParameter('val', 1)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByLevelAlertTwo()
    {
        return $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->andWhere('a.levelAlert = :val')
            ->setParameter('val', 2)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deleteArchive(int $alertId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT public.archive_alert(:alert_id)
    ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['alert_id' => $alertId]);

        return $stmt->fetchAll();
    }

    public function deleteAllArchive(int $alertId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT public.archive_all_alerts_same_computer(:alert_id)
    ';

        $stmt = $conn->prepare($sql);
        $stmt->execute(['alert_id' => $alertId]);

        return $stmt->fetchAll();
    }

    public function addToComputerAuthorizedCountry(int $addressIpId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT public.add_to_computer_authorized_country(:address_ip_id)
    ';

        $stmt = $conn->executeQuery($sql, ['address_ip_id' => $addressIpId]);

        return $stmt->fetchAll();
    }


    public function allowAllLocations(int $computerId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT public.allow_all_locations(:computer_id)
    ';

        $stmt = $conn->executeQuery($sql, ['computer_id' => $computerId]);

        return $stmt->fetchAll();
    }

    public function countAlertsByComputer(int $computerId): int
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
    SELECT public.count_alerts_by_computer(:computer_id)
    ';

        return $conn->executeQuery($sql, ['computer_id' => $computerId])->fetchOne();
    }

}