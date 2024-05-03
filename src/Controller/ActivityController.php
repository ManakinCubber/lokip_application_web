<?php
// src/Controller/ActivityController.php

namespace App\Controller;

use App\Repository\AddressIpRepository;
use App\Repository\ComputerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\LoggerInterface;
use App\Service\HectorApi;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ActivityController extends AbstractController
{
    private $AddressIpRepository;
    private $computerRepository;
    private $logger;
    private $hectorApi;
    private $cache;

    public function __construct(AddressIpRepository $AddressIpRepository, ComputerRepository $computerRepository, LoggerInterface $logger, HectorApi $hectorApi, CacheInterface $cache)
    {
        $this->AddressIpRepository = $AddressIpRepository;
        $this->computerRepository = $computerRepository;
        $this->logger = $logger;
        $this->hectorApi = $hectorApi;
        $this->cache = $cache;
    }

    /**
     * @Route("/activity", name="activity")
     */
    public function activityAction(Request $request, PaginatorInterface $paginator): Response
    {
        $computers = $this->computerRepository->findAll();
        $countries = $this->AddressIpRepository->findAllCountries();

        $selectedCountryNames = $request->query->get('countriesExport');

        $defaultOrder = 'desc';

        $order = $request->query->get('order', $defaultOrder);

        if (!in_array($order, ['asc', 'desc'])) {
            $order = $defaultOrder;
        }

        $queryBuilder = $this->AddressIpRepository->createQueryBuilder('a')
            ->leftJoin('a.computer', 'c')
            ->addSelect('c')
            ->orderBy('a.collectionDateTime', $order);

        // If countries are selected, convert them to AuthorizedCountry objects and add a condition to the query
        if ($selectedCountryNames && !empty($selectedCountryNames)) {
            $selectedCountries = $this->getDoctrine()->getRepository(\App\Entity\AuthorizedCountry::class)->findBy(['countryName' => $selectedCountryNames]);
            $queryBuilder->andWhere('a.country IN (:countries)')
                ->setParameter('countries', $selectedCountries);
        }

        $query = $queryBuilder->getQuery();

        $rows = $request->query->getInt('rows', 100);
        $days = $request->query->getInt('days', 7);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            $rows
        );

        if(count($pagination) == 0){
            $this->addFlash('error', 'Aucune addresse IP trouvée.');
        }

        $accessToken = $this->hectorApi->getAccessToken();

        $tags = [];

        foreach ($pagination as $AddressIp) {
            $hostname = $AddressIp->getComputer()->getHostname();
            $tag = $this->cache->get('tag_' . $hostname, function (ItemInterface $item) use ($hostname) {
                $item->expiresAfter(86400);
                $accessToken = $this->hectorApi->getAccessToken();
                return $this->hectorApi->getAccountFromHostname($accessToken, $hostname);
            });
            $AddressIp->tag = $tag;
            $collectionDateTime = $AddressIp->getCollectionDateTime();
            $tags[] = $tag;
        }

        return $this->render('activity.html.twig', [
            'pagination' => $pagination,
            'selectedDays' => $days,
            'selectedRows' => $rows,
            'countries' => $countries,
            'tags' => $tags,
            'computers' => $computers,
            'selectedCountries' => $selectedCountryNames,
            'order' => $order,
        ]);
    }


    /**
     * @Route("/activity/update-database", name="update_database", methods={"POST"})
     */
    public function updateDatabase(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['countries'])) {
            return new JsonResponse(['status' => 'error', 'message' => 'No countries provided.'], Response::HTTP_BAD_REQUEST);
        }

        foreach ($data['countries'] as $country) {
            $AddressIps = $this->AddressIpRepository->findByCountries([$country]);

            foreach ($AddressIps as $AddressIp) {
            }

            $this->getDoctrine()->getManager()->flush();
        }

        return new JsonResponse(['status' => 'success', 'message' => 'Database updated successfully.']);
    }
}