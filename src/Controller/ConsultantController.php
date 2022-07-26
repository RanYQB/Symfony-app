<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function profile($token): Response
    {
        return dd($token);
    }

}
