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

    public function add(string $tag): self // Function is static, returning a new TagList for immutability
    {
        $tag = trim($tag);

        if ($tag === '' || in_array($tag, $this->tags, true)) {
            return $this;
        } // If the tag is empty or already in the selection, nothing happens

        return new self([...$this->tags, $tag]); //if it is a truly new tag, then we add it to the tags array
    }

    public function remove(string $tag): self
    {
        return new self(array_values(array_diff($this->tags, [$tag])));
        // We make a new array without the tag thanks to array_diff, and then we remake that array to reorder the indexes correctly
    }

    public function toArray(): array
    {
        return $this->tags;
    }
}
