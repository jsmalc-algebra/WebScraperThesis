<?php

namespace App\CustomExceptions;

final class PageFetchFailedException extends \RuntimeException
{
    public function __construct(public readonly string $url, ?\Throwable $previous = null)
    {
        parent::__construct("Browser fetch failed: $url", 0, $previous);
    }
}
