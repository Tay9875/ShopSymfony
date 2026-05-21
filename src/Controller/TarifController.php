<?php
namespace App\Controller;

use App\Service\TarifService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TarifController extends AbstractController
{
    public function __construct(private TarifService $tarifService)
    {
    }

    #[Route('/tarif', name: 'tarif')]
    public function index(): Response
    {
        return $this->render('tarif/index.html.twig', [
            'taux'  => $this->tarifService->getTauxTva(),
            'ttc'   => $this->tarifService->htToTtc(100),
            'ht'    => $this->tarifService->ttcToHt(120),
        ]);
    }
}