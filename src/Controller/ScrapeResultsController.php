<?php

namespace App\Controller;

use App\Scraper\PageFetcher;
use App\Scraper\PageScraper;
use App\Scraper\TagList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_FREE')]
final class ScrapeResultsController extends AbstractController
{


    public function __construct(private readonly PageFetcher $fetcher){}

    #[Route('/scrape/results', name: 'app_scrape_results')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $link = $session->get('url');
        $html = $this->fetcher->getHtml($link);

        if ($session->has('tags')) {$tags = TagList::fromArray($session->get('tags'));}
        else {$tags = TagList::defaults();}

        $scraper = new PageScraper($html,$link);

        $newTag = $request->request->getString('tag');
        if ($newTag !== '' && $scraper->isValidSelector($newTag)) {
            $tags = $tags->add($newTag);

            if ($scraper->count($newTag) === 0) {
                $this->addFlash('warning', "'$newTag' matched nothing on this page.");
            }
        }

        elseif ($remove = $request->request->get('remove')) {
            $tags = $tags->remove($remove);
        }

        $session->set('tags', $tags->toArray()); // Save the array the class is representing back to the session

        if($request->isMethod('POST')) {
            return $this->redirectToRoute('app_scrape_results'); // If there were changes to the tags we refresh the page to have them be visible
        }

        $rows = [];

        foreach ($tags->toArray() as $tag) {
            $rows[] = [
                'tag'   => $tag,
                'count' => $scraper->count($tag),
            ];
        }

        return $this->render('scrape_results/index.html.twig', [
            'rows' => $rows,
            'title' => $scraper->title()
        ]);
    }
}
