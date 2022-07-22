<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Form\CandidateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/candidat', name: 'app_candidate_')]
class CandidateController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('candidate/candidate.html.twig', [
            'controller_name' => 'CandidateController',
        ]);
    }

    #[Route('/profil', name: 'profile')]
    public function profile(): Response
    {
        return $this->render('candidate/profile.html.twig', [
            'controller_name' => 'CandidateController',
        ]);
    }

    #[Route('/candidatures', name: 'applications')]
    public function apply(): Response
    {
        return $this->render('candidate/applications.html.twig', [
            'controller_name' => 'CandidateController',
        ]);
    }

    #[Route('/completer-profil', name: 'complete_profile')]
    public function completeProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $candidat = new Candidate();
        $form = $this->createForm(CandidateType::class, $candidat);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($candidat);
            $entityManager->flush();
        }

        return $this->render('candidate/profileForm.html.twig', [
            'Candidate_profile_form' => $form->createView(),
        ]);
    }
}
