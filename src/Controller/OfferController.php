<?php

namespace App\Controller;


use App\Repository\OfferRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    #[Route('/offres-d-emploi', name: 'app_offer')]
    public function index(OfferRepository $offerRepository): Response
    {
        $offer = $offerRepository->findBy(['isPublished' => true], ['created_at' => 'ASC']);

        return $this->render('offer/offer.html.twig', [
            'offers' => $offer,
        ]);
    }
}
