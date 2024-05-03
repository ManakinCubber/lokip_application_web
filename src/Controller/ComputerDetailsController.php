<?php
// src/Controller/ComputerDetailsController.php

namespace App\Controller;

use App\Entity\ComputerAuthorizedCountry;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AddressIpRepository;
use App\Repository\ComputerRepository;
use App\Service\HectorApi;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use League\Csv\CannotInsertRecord;
use League\Csv\Writer;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Doctrine\DBAL\Connection;
use App\Repository\AlertRepository;



class ComputerDetailsController extends AbstractController
{
    private $AddressIpRepository;
    private $computerRepository;
    private $hectorApi;
    private $cache;
    private $alertRepository;


    public function __construct(AlertRepository $alertRepository, AddressIpRepository $AddressIpRepository, ComputerRepository $computerRepository, HectorApi $hectorApi, CacheInterface $cache, Connection $connection)
    {
        $this->AddressIpRepository = $AddressIpRepository;
        $this->computerRepository = $computerRepository;
        $this->hectorApi = $hectorApi;
        $this->cache = $cache;
        $this->connection = $connection;
        $this->alertRepository = $alertRepository;

    }

    /**
     * @Route("/computer/{hostname}", name="computer_details")
     */
    public function computerDetailsAction(string $hostname, Request $request, PaginatorInterface $paginator): Response
    {
        $computer = $this->computerRepository->findOneBy(['hostname' => $hostname]);
        if (!$computer) {
            throw $this->createNotFoundException('No computer found for hostname ' . $hostname);
        }

        $computers = $this->computerRepository->findAll();
        $alertCount = $this->alertRepository->countAlertsByComputer($computer->getId());

        $tag = $this->cache->get('tag_' . $hostname, function(ItemInterface $item) use ($hostname) {
            $item->expiresAfter(86400);
            $accessToken = $this->hectorApi->getAccessToken();
            return $this->hectorApi->getAccountFromHostname($accessToken, $hostname);
        });

        $query = $this->AddressIpRepository->createQueryBuilder('a')
            ->where('a.computer = :computer')
            ->setParameter('computer', $computer)
            ->orderBy('a.collectionDateTime', 'DESC')
            ->getQuery();

        $rows = $request->query->getInt('rows', 10);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $rows
        );


        $authorizedCountries = $this->getDoctrine()
            ->getRepository(ComputerAuthorizedCountry::class)
            ->findByComputerId($computer->getId());


        return $this->render('computer_details.html.twig', [
            'computer' => $computer,
            'computers' => $computers,
            'tag' => $tag,
            'ipAddresses' => $pagination,
            'alertCount' => $alertCount,
            'authorizedCountries' => $authorizedCountries,
        ]);
    }

    /**
     * @Route("/export/computer/{hostname}", name="export_computer")
     * @throws CannotInsertRecordS
     */
    public function exportComputer(string $hostname, ComputerRepository $computerRepository, Connection $connection): Response
    {
        $computer = $computerRepository->findOneBy(['hostname' => $hostname]);

        if (!$computer) {
            throw $this->createNotFoundException('No computer found for hostname ' . $hostname);
        }

        $sql = "SELECT * FROM export_computer(:hostname)";
        $stmt = $connection->executeQuery($sql, ['hostname' => $hostname]);

        $ipAddresses = $stmt->fetchAll();

        $csvData = [];
        foreach ($ipAddresses as $ipAddress) {
            $csvData[] = [
                'Numéro' => $ipAddress['hostname'],
                'Adresse IP' => $ipAddress['ip_address'],
                'Localisation' => $ipAddress['country'],
                'Heure de collection' => (new \DateTime($ipAddress['collection_datetime']))->format('H:i:s'),
                'Date de collecte' => (new \DateTime($ipAddress['collection_datetime']))->format('d-m-Y'),
                'Proxy' => $ipAddress['is_proxy'],
            ];
        }

        $csv = Writer::createFromString('');
        $csv->insertOne(['Numéro', 'Adresse IP', 'Localisation', 'Heure de collection', 'Date de collecte', 'Proxy',]);
        $csv->insertAll($csvData);

        $response = new Response($csv->getContent());
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="computer_' . $hostname . '.csv"');

        return $response;
    }
}