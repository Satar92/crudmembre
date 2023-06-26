<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Service\AppHelpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    // #[Route('/', name: 'app_home')]
    public function index(AppHelpers $app, ManagerRegistry $doctrine): Response
    {
        // dd($app->getUser());
        // dd($app);
        $app->installBdd();

        $db = $doctrine->getManager();
        $categories = $db->getRepository(Categorie::class)->findAll();

        // dd($categories);

        return $this->render('home/index.html.twig', [
            'cat' => $categories,
        ]);
    }
}
