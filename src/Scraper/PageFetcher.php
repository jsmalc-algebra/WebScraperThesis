<?php
namespace App\Scraper;


use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final readonly class PageFetcher
{
    public function __construct(
        private HttpClientInterface    $http,
        private CacheItemPoolInterface $cache,
        private RobotsGuard            $guard
    ) {}

    public function fetch(string $link): string
    {
        if (!$this->guard->isAllowed($link)) {
            throw new AccessDeniedHttpException();
        }

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
        elseif ($premium_mode) {

        }
        else {
            $html = $this->fetch($link);
            $item->set($html);
            $item->expiresAfter(600);
            $this->cache->save($item);
        }

        return $html;
    }
}
