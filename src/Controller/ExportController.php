<?php

namespace App\Controller;

use App\Scraper\PageFetcher;
use App\Scraper\PageScraper;
use App\Scraper\TagList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[Route('/scrape/results/export')]
final class ExportController extends AbstractController
{
    public function __construct(private readonly PageFetcher $fetcher){}

    #[Route('/html', name: 'app_export_html', methods: ['GET'])]
    public function htmlExport(Request $request): Response
    {
        $loaded = $this->load($request);

        if ($loaded instanceof Response) {
            return $loaded;   // If there's no URL in session it redirects as there's nothing to export
        }

        $response = new Response($loaded['html'], 200, [
            'Content-Type'           => 'text/html; charset=utf-8',
            'X-Content-Type-Options' => 'nosniff',
        ]);
        // No sniff is redundant and serves as a safety net telling the browser to trust that content type is correct

        return $this->asDownload($response, $loaded['scraper'], 'html');

    }

    #[Route('/json', name: 'app_export_json', methods: ['GET'])]
    public function jsonExport(Request $request): Response
    {
        $loaded = $this->load($request);

        if ($loaded instanceof Response) {
            return $loaded; // Ditto
        }

        $session = $request->getSession();

        if ($session->has('tags')) {$tags = TagList::fromArray($session->get('tags'));}
        else {$tags = TagList::defaults();}

        $data = $loaded['scraper']->extract($tags->toArray());

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES //Human readability options
        );
        $response->headers->set('X-Content-Type-Options', 'nosniff'); //Ditto

        return $this->asDownload($response, $loaded['scraper'], 'json');
    }


    private function load(Request $request): array|Response
    {
        $session = $request->getSession();
        $url = $session->get('url');

        if (!$url) {
            return $this->redirectToRoute('URL_input');   //TODO: Make this redirect to an error page
        }

        $premium_mode = $session->get('premium_mode', false) && $this->isGranted('ROLE_PREMIUM');

        $html = $this->fetcher->getHtml($url, $premium_mode);

        return [
            'html'    => $html,
            'scraper' => new PageScraper($html,$url),
        ];
    }

    private function asDownload(Response $response, PageScraper $scraper, string $extension): Response
    {
        $slug =
            new AsciiSlugger()
            ->slug($scraper->title())
            ->truncate(60)
            ->toString(); // Gets rid of special characters by making the filename ASCII only

        $filename = sprintf('%s-%s.%s', $slug ?: 'scrape', date('Y-m-d_H-i-s'), $extension);
        // If for some reason the page doesn't have a title we provide one as "scrape" for filename purposes

        $response->headers->set(
            'Content-Disposition',
            HeaderUtils::makeDisposition(HeaderUtils::DISPOSITION_ATTACHMENT, $filename)
        ); //Adds either the HTML or JSON as attachment to the header

        return $response;
    }
}
