<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RennveranstaltungsController extends AbstractController
{
    #[Route('/rennveranstaltungs', name: 'app_rennveranstaltungs')]
    public function index(): Response
    {
        return $this->render('rennveranstaltungs/index.html.twig', [
            'controller_name' => 'RennveranstaltungsController',
        ]);
    }
}
