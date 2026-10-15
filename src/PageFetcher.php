<?php
namespace App\Scraper;


use App\BotIdentity;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PageFetcher
{
    public function __construct(private HttpClientInterface $http) {}

    public function fetch(string $url): string
    {
        return $this->http->request('GET', $url, [
            'headers' => ['User-Agent' => BotIdentity::USER_AGENT],
            'timeout' => 15,
        ])->getContent();
    }
}
