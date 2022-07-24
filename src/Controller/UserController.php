<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/profil', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/user.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/profil/completer-profil', name: 'app_user_complete_profile')]
    public function completeProfile(): Response
    {
        return $this->render('user/user_profile_fill.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
