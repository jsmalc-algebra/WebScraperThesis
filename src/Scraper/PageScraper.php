<?php

namespace App\Scraper;

use Symfony\Component\CssSelector\Exception\SyntaxErrorException;
use Symfony\Component\DomCrawler\Crawler;

class PageScraper
{
    private Crawler $crawler;
    private readonly string $html;

    public function __construct(string $html, string $URL)
    {
        $this->crawler = new Crawler($html, $URL);
        $this->html = $html;
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

    public function looksRendered(int $minLength = 500): bool
    {
        $copy = new Crawler($this->html);
        $copy->filter('script, style, noscript, template')->each(function (Crawler $n) { // We sort by the tags we don't need
            $node = $n->getNode(0); //For each tag found we take it's raw PHP DOM out of the crawler wrapper to access it's delete method
            $node->parentNode?->removeChild($node); //We look for the node's parent, tell it to remove the node, if no parent nothing happens
        });

        return strlen(trim(preg_replace('/\s+/', ' ', $copy->filter('body')->text('')))) >= $minLength;
        // We then remove any whitespace such as '\n', as well as the trailing ones that remain after
        //Once that's finished we count it against our min length to determine if it meets out standards
    }

}
