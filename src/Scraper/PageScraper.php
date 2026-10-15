<?php

namespace App\Scraper;

use Symfony\Component\CssSelector\Exception\SyntaxErrorException;
use Symfony\Component\DomCrawler\Crawler;

class PageScraper
{
    private Crawler $crawler;

    public function __construct(string $html, string $URL)
    {
        $this->crawler = new Crawler($html, $URL);
    }

    public function title(): string
    {
        return trim($this->crawler->filter('title')->text('')); # If there's no title nothing is returned
    }

    public function count(string $selector): int
    {
        return $this->crawler->filter($selector)->count(); # Counts how many tags there are for the given tag selector
    }

    public function isValidSelector(string $selector): bool
    {
        try {
            $this->crawler->filter($selector);
            return true;
        } catch (SyntaxErrorException) {
            return false;
        }
    }

    public function extract(array $selectors): array
    {
        $result = [];

        foreach ($selectors as $selector) {
            $nodes = $this->crawler->filter($selector);

            if ($nodes->count() > 0) {
                $result[$selector] = $nodes->each(fn (Crawler $n) => $n->outerHtml());
            }
        }

        return $result;
    }

}
