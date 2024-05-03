<?php
// src/Service/IP2LocationAPI.php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class IP2LocationAPI
{
    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @Route("/getCountryFromIp", name="getCountryFromIp")
     * @throws GuzzleException
     */
    public function getCountryFromIp(): string
    {
        $client = new Client([
            'verify' => false,
        ]);

        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();
        $sql = 'SELECT ip_address FROM Addressip LIMIT 1';
        $stmt = $conn->prepare($sql);
        $result = $stmt->execute();
        $data = $result->fetchAllAssociative();
        $ipAddress = $data[0]['ip_address'] ?? null;

        if (!$ipAddress) {
            return 'No IP address found in the database.';
        }

        $response = $client->request('GET', 'https://api.ip2location.io/?key=' . $_ENV['IP2LOCATION_API_KEY'] . '&ip=' . $ipAddress);

        if ($response->getStatusCode() !== 200) {
            return 'Failed to get location data from IP2Location API.';
        }

        $locationData = json_decode($response->getBody()->getContents(), true);

        $country = $locationData['country_name'] ?? null;

        if (!$country) {
            return 'No country data found for the IP address.';
        }
        return $country;
    }
}