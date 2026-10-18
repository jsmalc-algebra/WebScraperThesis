<?php
namespace App\Scraper;


use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PageFetcher
{
    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly CacheItemPoolInterface $cache,
        private readonly RobotsGuard $guard
    ) {}

    public function fetch(string $link): string
    {
        if (!$this->guard->isAllowed($link)) {
            throw new AccessDeniedHttpException(); //TODO: Make this a custom exception
        }

        return $this->http->request('GET', $link, [
            'headers' => ['User-Agent' => BotIdentity::USER_AGENT],
            'timeout' => 15,
        ])->getContent();
    }

    function getHtml(string $link): string
    {
        $item = $this->cache->getItem('page_' . sha1($link));

        if ($item->isHit()) {
            $html = $item->get();
        } else {
            $html = $this->fetch($link);
            $item->set($html);
            $item->expiresAfter(600);
            $this->cache->save($item);
        }

        return $html;
    }
}
