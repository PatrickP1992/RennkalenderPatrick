<?php

namespace App\Controller;


use App\Entity\Rennveranstaltung;
use App\Form\RennveranstaltungType;
use DateTime;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Repository\RennveranstaltungRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/api/v1', name: 'api.')]
class ApiController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/events', name: 'events_get', methods: ['GET'])]
    public function getEvents(RennveranstaltungRepository $rennveranstaltungRepository, Request $request): Response
    {
        $em = $this->doctrine->getManager();
        $rennveranstaltungen = $rennveranstaltungRepository->findAll();

        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);

        $data = $serializer->serialize($rennveranstaltungen, 'json');
        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_OK);
        $response->setContent($data);

        return $response;
    }

    #[Route('/event/{id}', name: 'event_get', methods: ['GET'])]
    public function getEvent($id, RennveranstaltungRepository $rennveranstaltungRepository, Request $request)
    {
        $em = $this->doctrine->getManager();
        $rennveranstaltung = $rennveranstaltungRepository->find($id);


        // Wen unter der id nichts gefunden wurde
        if ($rennveranstaltung == ''){
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->setContent('Die Angegebene ID ist ungültig oder exisiert nicht.');

            return $response;
        }

        $object = new \stdClass();
        $object->id = $rennveranstaltung->getId();
        $object->name = $rennveranstaltung->getName();
        $object->location = $rennveranstaltung->getLocation();
        $object->date = $rennveranstaltung->getDate();

        $data = json_encode($object);

        $response = new JsonResponse();
        $response->setStatusCode(Response::HTTP_OK);
        $response->setContent($data);

        return $response;
    }

    #[Route('/event/{id}', name: 'event_delete', methods: ['DELETE'])]
    public function deleteEvent($id, RennveranstaltungRepository $rennveranstaltungRepository, Request $request)
    {
        $em = $this->doctrine->getManager();
        $rennveranstaltung = $rennveranstaltungRepository->find($id);

        // Wen unter der id nichts gefunden wurde
        if ($rennveranstaltung == ''){
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->setContent('Die Angegebene ID ist ungültig oder exisiert nicht.');

            return $response;
        }

        // loschen
        $em->remove($rennveranstaltung);
        // Datenbank aktualisieren
        $em->flush();

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    #[Route('/event', name: 'event_post', methods: ['POST'])]
    public function postEvent(Request $request)
    {
       $requestData = $request->getPayload();
       $name = $requestData->get('name');
       $location = $requestData->get('location');
       $date = $requestData->get('date');

        // Überprüfe ob Date das richtige Format hat
        if (DateTime::createFromFormat('Y-m-d H:i:s', $date) !== false) {
            // it's a date
        }else{
            $response = new Response();
            $response->setStatusCode(Response::HTTP_NOT_FOUND);
            $response->setContent('Die Angegebene Datum ist nicht im richtigen Format.');

            return $response;
        }

       $date = \DateTime::createFromFormat('Y-m-d h:m:s', $date);

       $rennveranstaltung = new Rennveranstaltung();
       $rennveranstaltung->setName($name);
       $rennveranstaltung->setLocation($location);
       $rennveranstaltung->setDate($date);

        // Entity Manager
        $em = $this->doctrine->getManager();

        // Daten speichern
        $em->persist($rennveranstaltung);
        $em->flush();

        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    #[Route('/event/{id}', name: 'event_put', methods: ['PUT'])]
    public function putEvent($id, RennveranstaltungRepository $rennveranstaltungRepository, Request $request)
    {
        $em = $this->doctrine->getManager();
        $rennveranstaltung = $rennveranstaltungRepository->find($id);

        $data = json_decode($request->getContent(), true);

        // Formular
        $form = $this->createForm(RennveranstaltungType::class, $rennveranstaltung);
        $form->submit($data);


        // Daten speichern
        $em->persist($rennveranstaltung);
        $em->flush();


        $response = new Response();
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }
}
