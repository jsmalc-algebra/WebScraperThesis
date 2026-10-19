<?php

namespace App\Scraper;

use Spatie\Robots\RobotsTxt;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class RobotsGuard
{

    public function __construct(
        private HttpClientInterface $httpClient,
        private CacheInterface $cache,
    )
    {}

    public function isAllowed(string $URL): bool {
        $parsedUrl = parse_url($URL);

        if (!isset($parsedUrl['scheme'], $parsedUrl['host']) || !in_array($parsedUrl['scheme'], ['http', 'https'], true)) {
            throw new \InvalidArgumentException("Not an http(s) URL: $URL");
        } // If the URL given doesn't start with HTTP or HTTPS, or if the given URL even has a scheme/host

        $origin = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] . (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '');
        // We reconstruct the link down to the root as that is where robots/txt "live"

        $cacheKey = 'robots_' . sha1($origin); // Cache the robots text based on  it's URL

        $downloadIfMissing = function (ItemInterface $item) use ($origin) {
            $item->expiresAfter(3600);// Have the cache last an hour
            return $this->download($origin . '/robots.txt'); // Fetch Robots
        };

        $body = $this->cache->get($cacheKey, $downloadIfMissing);
        // Check if we already have robots cached, if not download them

        $body = preg_replace('/\R/', PHP_EOL, $body); //just in case, make sure the end of line matches what PHP is using

        return new RobotsTxt($body)->allows($URL, BotIdentity::BOT_TOKEN);
    }

    private function download(string $robotsUrl): string
    {
        $response = $this->httpClient->request('GET', $robotsUrl,[
            'headers' => ['User-Agent' => BotIdentity::USER_AGENT],
            'timeout' => 10
        ]);

        $status = $response->getStatusCode();

        if ($status >= 200 && $status < 300) {
            return $response->getContent();
        }
        if ($status >= 400 && $status < 500) {
            return ''; // There is no robots file, meaning we are free to scrape
        }

        throw new \RuntimeException("Could not download robots.txt ($status): $robotsUrl");
    }


}
