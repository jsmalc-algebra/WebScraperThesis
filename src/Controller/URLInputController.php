<?php

namespace App\Controller;

use App\Form\LinkInputFormType;
use App\RobotsGuard;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class URLInputController extends AbstractController
{
    public function __construct(private RobotsGuard $guard) {}

    #[Route('/enter-url', name: 'URL_input')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(LinkInputFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $link = $this->normalizeUrl(
                $form->getData('link')->getData()
            );

            if (!$this->guard->isAllowed($link)) {
                throw new AccessDeniedHttpException(); //TODO: Make this a custom exception
            }
        }

        return $this->render('url_input/index.html.twig', [
            'linkInputForm' => $form,
        ]);
    }

    function normalizeUrl(string $input): string
    {
        $input = trim($input);

        // Already has a scheme (http://, ftp://, file://...)? Leave it alone.
        if (preg_match('~^[a-z][a-z0-9+.-]*://~i', $input)) {
            return $input;
        }

        // No scheme: assume https
        return 'https://' . ltrim($input, '/');
    }
}
