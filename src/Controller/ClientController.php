<?php
namespace App\Controller;

use App\Entity\Client;
use App\Service\FideliteService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientController extends AbstractController
{
    public function __construct(private FideliteService $fideliteService)
    {
    }

    #[Route('/client/{id}/score', name: 'client_score')]
    public function score(Client $client): Response
    {
        $score = $this->fideliteService->calculerScore($client);
        $niveau = $this->fideliteService->getNiveau($score);

        return $this->render('client/score.html.twig', [
            'client' => $client,
            'score'  => $score,
            'niveau' => $niveau,
        ]);
    }
}