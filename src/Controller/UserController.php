<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Offer;
use App\Entity\Recruiter;
use App\Entity\User;
use App\Form\CandidateType;
use App\Form\OfferType;
use App\Form\RecruiterType;
use App\Repository\CandidateRepository;
use App\Repository\OfferRepository;
use App\Repository\RecruiterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class UserController extends AbstractController
{


    #[Route('/profil/profil-incomplet', name: 'app_user_incomplete_profil')]
    public function index(): Response
    {
        if($this->getUser()->getCandidate()){
            $this->redirectToRoute('app_user');
        } elseif ($this->getUser()->getRecruiter()){
            $this->redirectToRoute('app_user');
        }
        return $this->render('user/user_incomplete.html.twig');
    }

    #[Route('/profil', name: 'app_user')]
    public function profile(CandidateRepository $candidateRepository, RecruiterRepository $recruiterRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();

        if($this->isGranted('ROLE_CANDIDATE'))
        {
            if($user->getCandidate() === null){
                return $this->redirectToRoute('app_user_incomplete_profil');
            }
        } elseif ($this->isGranted('ROLE_RECRUITER')){
            if($user->getRecruiter() === null){
                return $this->redirectToRoute('app_user_incomplete_profil');
            }
        }


        if($this->isGranted('ROLE_CANDIDATE')){
            $candidate = $candidateRepository->find($this->getUser()->getCandidate());
            $options = [
                'candidate' => $candidate,
            ];

        } elseif ($this->isGranted('ROLE_RECRUITER')){
            $recruiter = $recruiterRepository->find($this->getUser()->getRecruiter());
            $options = [
                'recruiter' => $recruiter,
            ];
        };

        return $this->render('user/user.html.twig', $options);
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

                $recruiter->setUser($this->getUser());
                $entityManager->persist($recruiter);
                $entityManager->flush();

                $user = $this->getUser();
                $user->setRecruiter($recruiter);

                $entityManager->persist($user);
                $entityManager->flush();

                return $this->redirectToRoute('app_user');
            };
        };

        return $this->render('user/user_profile_fill.html.twig', [
            'profileFillForm' => $form->createView(),
        ]);
    }

    public function __construct(private SluggerInterface $slugger){}

    #[Route('/recruteur/ajouter-une-offre', name: 'app_recruiter_new_offer')]
    public function AddOffer(Request $request, EntityManagerInterface $entityManager): Response
    {
        if($this->isGranted('ROLE_RECRUITER')){
            $offer = new Offer();

            $form = $this->createForm(OfferType::class, $offer);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                $offer->setIsPublished(false);
                $offer->setSlug($this->slugger->slug($offer->getTitle())->lower());
                $offer->setRecruiter($this->getUser()->getRecruiter());
                $entityManager->persist($offer);
                $entityManager->flush();

                return $this->redirectToRoute('app_user');
            };

        } else {
            $this->redirectToRoute('app-user');
        }
        return $this->render('user/add_offer.html.twig',[
            'offerForm' => $form->createView(),
    ]);
    }


    #[Route('/recruteur/offres-publiees', name: 'app_recruiter_offers')]
    public function showOffers(OfferRepository $offerRepository): Response
    {
        if($this->isGranted('ROLE_RECRUITER'))
        {
            $offers = $offerRepository->findBy(['recruiter' => $this->getUser()->getRecruiter()], ['created_at' => 'ASC']);
        }
        else
        {
            $this->redirectToRoute('app-user');
        }

        return $this->render('user/show_offers.html.twig', [
            'offers' => $offers
        ]);

    }

}
