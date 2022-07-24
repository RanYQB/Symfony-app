<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Recruiter;
use App\Entity\User;
use App\Form\CandidateType;
use App\Form\RecruiterType;
use App\Repository\CandidateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class UserController extends AbstractController
{
    private $security;

    #[Route('/profil', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/user.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/profil/completer-profil', name: 'app_user_complete_profile')]
    public function completeProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isGranted('ROLE_CANDIDATE')){
            $candidate = new Candidate();

            $form = $this->createForm(CandidateType::class, $candidate);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $candidate->setUser($this->getUser());
                $entityManager->persist($candidate);
                $entityManager->flush();

                $user = $this->getUser();
                $user->setCandidate($candidate);

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_user');
            };





        } elseif ($this->isGranted('ROLE_RECRUITER')) {
            $recruiter = new Recruiter();

            $form = $this->createForm(RecruiterType::class, $recruiter);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $entityManager->persist($recruiter);
                $entityManager->flush();
            };
        };

        return $this->render('user/user_profile_fill.html.twig', [
            'profileFillForm' => $form->createView(),
        ]);
    }
}
