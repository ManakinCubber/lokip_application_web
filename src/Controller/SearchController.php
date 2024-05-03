<?php
// src/Controller/SearchController.php

namespace App\Controller;

use App\Repository\ComputerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    private $computerRepository;

    public function __construct(ComputerRepository $computerRepository)
    {
        $this->computerRepository = $computerRepository;
    }


    /**
     * @Route("/search", name="search_hostname", methods={"GET"})
     */
    public function searchFromHostname(Request $request)
    {
        $hostname = $request->query->get('hostname');

        return $this->redirectToRoute('computer_details', ['hostname' => $hostname]);
    }


}