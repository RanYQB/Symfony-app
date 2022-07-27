<?php

namespace App\Controller\Admin;

use App\Entity\Consultant;
use App\Form\ConsultantType;
use App\Repository\ConsultantRepository;
use App\Services\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(ConsultantRepository $consultantRepository): Response
    {

        return $this->render('admin/admin.html.twig', [
            'consultants' => $consultantRepository->findBy([], ['lastname'=>'asc'])
        ]);
    }

    #[Route('/admin/nouveau-consultant', name: 'app_admin_add')]
    public function newConsultant(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer, JWTService $jwt): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            $consultant = new Consultant();

            $form = $this->createForm(ConsultantType::class, $consultant);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $consultant->setIsActive(true);
                $entityManager->persist($consultant);
                $entityManager->flush();

                $header = [
                    'alg' => 'HS256',
                    'typ' => 'JWT'];

                $payload = [
                    'consultant_id' => $consultant->getId(),
                ];

                $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

                $email = (new TemplatedEmail())
                    ->from($this->getUser()->getUserIdentifier())
                    ->to($consultant->getEmail())
                    ->subject('Validez votre compte')
                    ->htmlTemplate('consultant/consultant_account_email.html.twig')
                    ->context([
                        'consultant'=> $consultant,
                        'token' => $token,
                        'nom' => $consultant->getLastname(),
                        'prenom' => $consultant->getFirstname(),

                    ]);
                $mailer->send($email);
                $this->addFlash('message', 'Votre e-mail a été envoyé.');
                return $this->redirectToRoute('app_admin_add');
            }

        }

        return $this->render('admin/admin_add.html.twig', [
            'consultantForm' => $form->createView(),
            ]
        );

    }
}
