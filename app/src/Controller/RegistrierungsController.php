<?php

namespace App\Controller;


use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrierungsController extends AbstractController
{
    private $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    #[Route('/reg', name: 'reg')]
    public function reg(Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $regForm = $this->createFormBuilder()
            ->add('username', TextType::class, ['label' => 'Username'])
            ->add('password', RepeatedType::class,
                [
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => ['label' => 'Passwort'],
                    'second_options' => ['label' => 'Passwort wiederholen']
                ])
            ->add('registrieren', SubmitType::class)
            ->getForm();

        $regForm->handleRequest($request);

        if ($regForm->isSubmitted() && $regForm->isValid())
        {
            $eingabe = $regForm->getData();

            $user = new User();
            $user->setUsername($eingabe['username']);
            $user->setPassword( $passwordEncoder->hashPassword($user, $eingabe['password']));

            // Entity Manager
            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('app_main');
        }

        return $this->render('registrierungs/index.html.twig', [
            'regform' => $regForm->createView()
        ]);
    }
}
