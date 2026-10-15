<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ScrapeResultsController extends AbstractController
{

    #[Route('/results', name: 'app_scrape_results')]
    public function index(string $html, string $URL ,Request $request): Response
    {

    }
}
