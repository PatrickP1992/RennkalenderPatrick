<?php

namespace App\Controller;

use App\Entity\Rennveranstaltung;
use App\Form\RennveranstaltungType;
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

        // Formular
        $form = $this->createForm(RennveranstaltungType::class, $rennveranstaltung);
        $form->handleRequest($request);

        // Wenn das Formular abgeschickt wurde
        if ($form->isSubmitted() && $form->isValid()) {
            // Entity Manager
            $em = $this->doctrine->getManager();

            // Daten speichern
            $em->persist($rennveranstaltung);
            $em->flush();

            return $this->redirectToRoute('rennveranstaltungen.bearbeiten');
        }


        // Response
        return $this->render('rennveranstaltungs/anlegen.html.twig', [
            'anlegenForm' => $form->createView(),
        ]);
    }

    #[Route('/edit{id}', name: 'edit')]
    public function edit($id, RennveranstaltungRepository $rennveranstaltungRepository, Request $request)
    {
        $em = $this->doctrine->getManager();
        $rennveranstaltung = $rennveranstaltungRepository->find($id);


        // Formular
        $form = $this->createForm(RennveranstaltungType::class, $rennveranstaltung);
        $form->handleRequest($request);
        //$form->setData($rennveranstaltung);

        // Wenn das Formular abgeschickt wurde
        if ($form->isSubmitted() && $form->isValid()) {
             print_r('Formular abschicken');
            // Entity Manager
            $em = $this->doctrine->getManager();

            // Daten speichern
            $em->flush();

            return $this->redirectToRoute('rennveranstaltungen.bearbeiten');
        }


        // Response
        return $this->render('rennveranstaltungs/edit.html.twig', [
            'editForm' => $form->createView(),
        ]);

    }

    #[Route('/entfernen{id}', name: 'entfernen')]
    public function entfernen($id, RennveranstaltungRepository $rennveranstaltungRepository)
    {
        // Entity Manager
        $em = $this->doctrine->getManager();
        $rennveranstaltung = $rennveranstaltungRepository->find($id);
        // loschen
        $em->remove($rennveranstaltung);
        // Datenbank aktualisieren
        $em->flush();

        // Nachricht
        $this->addFlash('erfolg', 'Rennveranstaltung erfolgreich entfernt');

        return $this->redirectToRoute('rennveranstaltungen.bearbeiten');
    }
}
