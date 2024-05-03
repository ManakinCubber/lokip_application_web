<?php
// src/Controller/ExportController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ComputerRepository;
use App\Repository\AddressIpRepository;
use App\Repository\AuthorizedCountryRepository;
use League\Csv\Writer;
use Psr\Log\LoggerInterface;


class ExportController extends AbstractController
{
    private $addressIpRepository;
    private $computerRepository;
    private $logger;

    public function __construct(AddressIpRepository $addressIpRepository, ComputerRepository $computerRepository, LoggerInterface $logger)
    {
        $this->addressIpRepository = $addressIpRepository;
        $this->computerRepository = $computerRepository;
        $this->logger = $logger;
    }

    /**
     * @Route("/export", name="export")
     */
    public function export(): Response
    {
        $computers = $this->computerRepository->findAll();
        $countries = $this->addressIpRepository->findAllCountries();

        return $this->render('export.html.twig', [
            'computers' => $computers,
            'countries' => $countries,
        ]);
    }

    /**
     * @Route("/export/data", name="export_data")
     */
    public function exportData(Request $request): Response
    {
        $hostnames = $request->query->get('NumeroExport');
        $startDate = $request->query->get('startDateExport');
        $endDate = $request->query->get('endDateExport');
        $countries = $request->query->get('countriesExport');

        if ($countries === null) {
            $countries = null;
        }

        $csvData = $this->addressIpRepository->generateCsvData($hostnames, $startDate, $endDate, $countries, $this->logger);

        $csv = Writer::createFromString('');
        $csv->insertOne(['hostname', 'ip_address', 'country_id', 'collection_datetime']);
        $csv->insertAll($csvData);

        $response = new Response($csv->getContent());
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

        return $response;
    }
}