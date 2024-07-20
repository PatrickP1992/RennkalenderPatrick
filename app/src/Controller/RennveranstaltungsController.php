<?php

namespace App\Controller;

use App\Entity\Rennveranstaltung;
use App\Repository\RennveranstaltungRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Persistence\ManagerRegistry;

#[Route('/rennveranstaltungen', name: 'rennveranstaltungen.')]
class RennveranstaltungsController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/', name: 'bearbeiten')]
    public function index(RennveranstaltungRepository $rennveranstaltungRepository): Response
    {
        $rennveranstaltungen = $rennveranstaltungRepository->findAll();

        return $this->render('rennveranstaltungs/index.html.twig', [
            'rennveranstaltungen' => $rennveranstaltungen,
        ]);
    }

    #[Route('/anlegen', name: 'anlegen')]
    public function anlegen(Request $request)
    {
        $rennveranstaltung = new Rennveranstaltung();
        $rennveranstaltung->setName('Fomel 1');

        // Entity Manager
        $em = $this->doctrine->getManager();
        $em->persist($rennveranstaltung);
        $em->flush();

        // Response
        return new Response('Rennveranstaltung wurde angelegt');
    }
}
