<?php
// src/Service/HectorApi.php

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class HectorApi
{
    private $client;

    public function __construct()
    {
        $this->client = new Client([
            'verify' => $_ENV['SSL_CERT_PATH'],
        ]);
    }

    public function getAccessToken(): string
    {
        $response = $this->client->request('POST', $_ENV['HECTOR_API_URL'] . '/auth/Token', [
            'headers' => [
                'Content-Type' => 'application/json',
                'hector-instance' => $_ENV['HECTOR_INSTANCE'],
            ],
            'json' => [
                'email' => $_ENV['HECTOR_EMAIL'],
                'password' => $_ENV['HECTOR_PASSWORD'],
            ]
        ]);

        $responseData = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== 200 || !isset($responseData['accessToken'])) {
            throw new RequestException('Failed to obtain access token', $response);
        }

        return $responseData['accessToken'];
    }

    public function getTag(string $accessToken): array
    {
        $response = $this->client->request('GET', $_ENV['HECTOR_API_URL'] . '/assets', [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'hector-instance' => $_ENV['HECTOR_INSTANCE'],
                'Content-Type' => 'application/json'
            ],
        ]);

        $responseData = json_decode($response->getBody()->getContents(), true);

        if ($response->getStatusCode() !== 200 || !isset($responseData['items'])) {
            throw new RequestException('Failed to retrieve asset data', $response);
        }

        return array_column($responseData['items'], 'tag');
    }

    public function getAccountFromHostname(string $accessToken, string $hostname): ?string
    {
        try {
            $userResponse = $this->client->request('GET', $_ENV['HECTOR_API_URL'] . '/tag/' . $hostname . '/asset/', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'hector-instance' => $_ENV['HECTOR_INSTANCE'],
                    'Content-Type' => 'application/json'
                ],
            ]);

            if ($userResponse->getStatusCode() !== 200) {
                return null;
            }

            $userData = json_decode($userResponse->getBody()->getContents(), true);
            $accountId = $userData['userId'] ?? null;

            if ($accountId <= 0) {
                return null;
            }

            $accountResponse = $this->client->request('GET', $_ENV['HECTOR_API_URL'] . '/users/' . $accountId, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'hector-instance' => $_ENV['HECTOR_INSTANCE'],
                    'Content-Type' => 'application/json'
                ],
            ]);

            if ($accountResponse->getStatusCode() !== 200) {
                return null;
            }

            $accountData = json_decode($accountResponse->getBody()->getContents(), true);
            $accountTag = $accountData['account'];

            return $accountTag;
        } catch (GuzzleException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}