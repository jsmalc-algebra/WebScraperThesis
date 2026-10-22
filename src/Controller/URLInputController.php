<?php

namespace App\Controller;

use App\Form\LinkInputFormType;
use App\LoggingSupport\LoggingActions;
use App\Scraper\PageFetcher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class URLInputController extends AbstractController
{
    public function __construct(
        private readonly PageFetcher         $pageFetcher,
        private readonly TranslatorInterface $translator,
    ) {}

    #[Route('/scrape', name: 'URL_input')]
    public function index(Request $request,LoggingActions $logger): Response
    {
        $form = $this->createForm(LinkInputFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $link = $this->normalizeUrl(
                $form->get('link')->getData()
            );

            try {
                $this->pageFetcher->fetch($link);
                $request->getSession()->set('url', $link);

                $logger->userScrape("URL: $link");

                return $this->redirectToRoute('app_scrape_results');
            } catch (AccessDeniedHttpException) {
                $form->get('link')->addError(new FormError($this->translator->trans('robots.error')));
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

