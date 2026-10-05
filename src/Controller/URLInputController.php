<?php

namespace App\Controller;

use App\Form\LinkInputFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class URLInputController extends AbstractController
{
    #[Route('/enter-url', name: 'URL_input')]
    public function index(): Response
    {
        $form = $this->createForm(LinkInputFormType::class);

        return $this->render('url_input/index.html.twig', [
            'linkInputForm' => $form,
        ]);
    }
}
