<?php
// src/Controller/AddressIpController.php

namespace App\Controller;

use App\Entity\AddressIp;
use App\Service\IP2LocationAPI;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddressIpController extends AbstractController
{
    private $doctrine;
    private $ip2LocationApi;

    public function __construct(ManagerRegistry $doctrine, IP2LocationAPI $ip2LocationApi)
    {
        $this->doctrine = $doctrine;
        $this->ip2LocationApi = $ip2LocationApi;
    }

    public function saveipAddress(string $ipAddress): void
    {
        $country = $this->ip2LocationApi->getCountryFromIp($ipAddress);

        $computer = $this->doctrine->getRepository(Computer::class)->findOneBy(['hostname' => 'hostname']);

        $AddressIp = new AddressIp();
        $AddressIp->setipAddress($ipAddress);
        $AddressIp->setCountry($country);
        $AddressIp->setComputer($computer);
        $AddressIp->collectionDateTime(new \DateTime());

        $em = $this->doctrine->getManager();
        $em->persist($AddressIp);
        $em->flush();
    }
}