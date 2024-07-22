<?php

namespace App\Controller;

use App\Repository\RennveranstaltungRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }
    #[Route('/', name: 'app_main')]
    public function index(RennveranstaltungRepository $rennveranstaltungRepository): Response
    {
        $rennveranstaltungen = $rennveranstaltungRepository->findAll();

        return $this->render('main/index.html.twig', [
            'rennveranstaltungen' => $rennveranstaltungen,
        ]);
    }
}
