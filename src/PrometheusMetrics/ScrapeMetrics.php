<?php

namespace App\PrometheusMetrics;

use Prometheus\CollectorRegistry;

class ScrapeMetrics
{
    public function __construct(private readonly CollectorRegistry $registry){}

    public function requestScrape(): void
    {
        $this->registry
            ->getOrRegisterCounter('scraper_app','request_scrape','number of scrape actions done via a GET request')
            ->inc();
    }

    public function headlessScrape(): void
    {
        $this->registry
            ->getOrRegisterCounter('scraper_app','headless_scrape','number of scrape actions done via a headless browser')
            ->inc();
    }
}
