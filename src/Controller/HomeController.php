<?php

namespace App\Controller;

use App\Entity\AddressIp;
use App\Repository\AddressIpRepository;
use App\Repository\ComputerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\AlertRepository;


class HomeController extends AbstractController
{
    private $addressIpRepository;
    private $computerRepository;
    private $alertRepository;
    private $cache;
    private $paginator;

    public function __construct(AddressIpRepository $addressIpRepository, ComputerRepository $computerRepository, AlertRepository $alertRepository, CacheInterface $cache, PaginatorInterface $paginator)
    {
        $this->addressIpRepository = $addressIpRepository;
        $this->computerRepository = $computerRepository;
        $this->alertRepository = $alertRepository;
        $this->cache = $cache;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/home", name="home")
     */
    public function index(Request $request): Response
    {
        $alerts = $this->alertRepository->findAllAlerts();

        $pagination = $this->paginator->paginate(
            $alerts,
            $request->query->getInt('page', 1),
            10
        );

        $totalOrangeAlerts = $this->alertRepository->countByLevelAlertZero();
        $totalRedAlerts = $this->alertRepository->countByLevelAlertOne();

        $computers = $this->computerRepository->findAll();

        $totalComputers = $this->computerRepository->getTotalComputers();

        $countryRanking = $this->addressIpRepository->getCountryRanking();

        return $this->render('home.html.twig', [
            'alerts' => $alerts,
            'totalOrangeAlerts' => $totalOrangeAlerts,
            'totalRedAlerts' => $totalRedAlerts,
            'computers' => $computers,
            'totalComputers' => $totalComputers,
            'countryRanking' => $countryRanking,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/delete-alerts/{id}", name="delete_alert")
     */
    public function deleteAlert(int $id, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            $conn = $em->getConnection();

            $sql = 'SELECT public.archive_alert(:alert_id)';
            $stmt = $conn->prepare($sql);
            $stmt->execute(['alert_id' => $id]);

            return new JsonResponse(['success' => true]);
        } else {
            return new JsonResponse(['error' => 'Invalid request'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/delete-all-alerts/{id}", name="delete_all_alerts")
     */
    public function deleteAllAlerts(int $id, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $result = $this->alertRepository->deleteAllArchive($id);

            if (!empty($result)) {
                return new JsonResponse(['success' => true]);
            } else {
                return new JsonResponse(['error' => 'Erreur lors de la suppression de toutes les alertes'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return new JsonResponse(['error' => 'Invalid request'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/authorize-country/{id}", name="authorize_country")
     */
    public function authorizeCountry(int $id, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            // Validate the id
            $alert = $this->alertRepository->find($id);
            if (!$alert) {
                return new JsonResponse(['error' => 'Invalid alert id'], JsonResponse::HTTP_BAD_REQUEST);
            }

            $result = $this->alertRepository->addToComputerAuthorizedCountry($id);

            if (!empty($result)) {
                return new JsonResponse(['success' => true]);
            } else {
                return new JsonResponse(['error' => 'Erreur lors de l\'autorisation du pays'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return new JsonResponse(['error' => 'Invalid request'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/stop-alerts/{id}", name="stop_alerts")
     */
    public function stopAlerts(int $id, Request $request): Response
    {
        if ($request->isXmlHttpRequest()) {
            $result = $this->alertRepository->allowAllLocations($id);

            if (!empty($result)) {
                return new JsonResponse(['success' => true]);
            } else {
                return new JsonResponse(['error' => 'Erreur lors de l\'arrêt des alertes'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            return new JsonResponse(['error' => 'Invalid request'], JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}