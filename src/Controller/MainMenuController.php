<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MainMenuController extends AbstractController
{
    #[Route('/', name: 'app_main_menu')]
    #[IsGranted('ROLE_FREE')]
    public function index(): Response
    {
        return $this->render('main_menu/index.html.twig');
    }
}
