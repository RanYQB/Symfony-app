<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ConsultantRegistrationFormType;
use App\Repository\ConsultantRepository;
use App\Security\AppLoginAuthenticator;
use App\Services\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class ConsultantController extends AbstractController
{
    #[Route('/consultant', name: 'app_consultant')]
    public function index(): Response
    {
        return $this->render('consultant/consultant.html.twig', [
            'controller_name' => 'ConsultantController',
        ]);
    }

    #[Route('/consultant/definir-mot-de-passe/{token}', name: 'app_consultant_profile')]
    public function createAccount($token, JWTService $jwt, ConsultantRepository $consultantRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppLoginAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret')))
        {
            $payload = $jwt->getPayload($token);

            $consultant = $consultantRepository->find($payload['consultant_id']);

            if($consultant && $consultant->getUser() === null){
                $user = new User();
                $form = $this->createForm(ConsultantRegistrationFormType::class, $user);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    // encode the plain password
                    $user->setEmail($consultant->getEmail());
                    $user->setConsultant($consultant);
                    $user->setRoles((array)'ROLE_CONSULTANT');
                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $form->get('plainPassword')->getData()
                        )
                    );

                    $entityManager->persist($user);
                    $entityManager->flush();

                    $consultant->setUser($user);
                    $entityManager->persist($consultant);
                    $entityManager->flush();

                    // do anything else you need here, like send an email

                    return $userAuthenticator->authenticateUser(
                        $user,
                        $authenticator,
                        $request
                    );


                }

            }

            return $this->render('consultant/consultant_account.html.twig', [
                'registrationForm' => $form->createView(),
                'consultant' => $consultant,
            ]);

        }

        $this->addFlash('danger', 'Le token est invalide ou a expiré !');
        return $this->redirectToRoute('app_home');

    }

}
