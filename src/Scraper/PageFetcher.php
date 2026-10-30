<?php
namespace App\Scraper;


use App\CustomExceptions\PageFetchFailedException;
use App\PrometheusMetrics\ScrapeMetrics;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Panther\Client as PantherClient;

final readonly class PageFetcher
{
    public function __construct(
        private HttpClientInterface    $http,
        private CacheItemPoolInterface $cache,
        private RobotsGuard            $guard,
        private BrowserFactory         $browserFactory,
        private ScrapeMetrics         $scrapeMetrics,
    ) {}

    public function fetch(string $link,bool $premium_mode=false): string
    {
        if (!$this->guard->isAllowed($link)) {
            throw new AccessDeniedHttpException();
        }

        elseif ($premium_mode) {
            $this->scrapeMetrics->headlessScrape();
            return $this->fetchWithBrowser($link);
        }

        else {$this->scrapeMetrics->requestScrape();}

        return $this->http->request('GET', $link, [
            'headers' => ['User-Agent' => BotIdentity::USER_AGENT],
            'timeout' => 15,
        ])->getContent();
    }

    function getHtml(string $link, bool $premium_mode = false): string
    {
        $key = 'page_'.($premium_mode ? 'browser_' : 'http_'). sha1($link);
        //2 keys depending on weather we fetch the page via regular crawler or Panther browser
        $item = $this->cache->getItem($key);

        if ($item->isHit()) {
            $html = $item->get();
        }
        else {
            $html = $this->fetch($link, $premium_mode);
            $item->set($html);
            $item->expiresAfter(600);
            $this->cache->save($item);
        }

        return $html;
    }

    private function fetchWithBrowser(string $link): string
    {
        $client = null;

        try {
            $client = $this->browserFactory->create(); //Start the browser
            $client->request('GET', $link); //Open the URL so the JS runs
            $this->waitUntilSettled($client); //Wait until page stops changing
            return $client->getPageSource(); //Return the page after JS runs, once again as an HTML string
        } catch (\Throwable $exception) {
            throw new PageFetchFailedException($link, $exception);
        } finally {
            $client?->quit(); //Make sure the browser is always closed in the end
        }
    }

    private function waitUntilSettled(PantherClient $client, int $maxSeconds = 10): void
    {
        $deadline = microtime(true) + $maxSeconds;
        $last = -1;
        $stable = 0;

        while (microtime(true) < $deadline) {
            $length = (int)$client->executeScript(
                'return document.body ? document.body.innerText.length : 0;'
            );

            $stable = ($length === $last) ? $stable + 1 : 0;

            if ($stable >= 3) {
                return;
            }

            $last = $length;
            usleep(500_000);
        }
    }

    }
