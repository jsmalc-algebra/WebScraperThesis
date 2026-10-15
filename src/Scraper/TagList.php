<?php

namespace App\Scraper;

class TagList
{
    private const DEFAULTS = ['title', 'h1', 'h2', 'h3', 'a', 'p', 'img'];

    private function __construct(private array $tags) {}

    public static function defaults(): self
    {
        return new self(self::DEFAULTS); // Make the class with the default list
    }

    public static function fromArray(array $tags): self
    {
        return new self(array_values($tags)); // Mostly meant to be used with session data
    }
}
