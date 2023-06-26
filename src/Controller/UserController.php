<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{

    // #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('profile/index.html.twig', []);
    }




}
