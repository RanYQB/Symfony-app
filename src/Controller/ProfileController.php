<?php

namespace App\Controller;

use App\Entity\Candidate;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProfileController extends AbstractController
{

    #[Route('/profil', name: 'app_profile')]
    public function index(): Response
    {
        return $this->render('profile/main.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }
}
