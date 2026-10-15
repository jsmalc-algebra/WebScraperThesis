<?php

namespace App\Controller;

use App\Form\LinkInputFormType;
use App\Scraper\PageFetcher;
use App\Scraper\RobotsGuard;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class URLInputController extends AbstractController
{
    public function __construct(
        private RobotsGuard $guard,
        private PageFetcher $fetcher,
        private CacheItemPoolInterface $cache
    ) {}

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

            $cacheKey = 'page_' . sha1($link);
            $item = $this->cache->getItem($cacheKey);

            if ($item->isHit()) {
                $html = $item->get();
            } else {
                $html = $this->fetcher->fetch($link);
                $item->set($html);
                $item->expiresAfter(600);
                $this->cache->save($item);
            }

            // TODO: make the scraping results page, and throw $html to it

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
