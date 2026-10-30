<?php

namespace App\PrometheusMetrics;

use Prometheus\CollectorRegistry;

class UserTierSwitchMetrics
{
    public function __construct(private readonly CollectorRegistry $registry){}

    public function userTierUpgrade(): void
    {
        $this->registry
            ->getOrRegisterCounter('scraper_app','userTierUpgrade','number of times users upgraded their tier to premium')
            ->inc();
    }

    public function userTierDowngrade(): void
    {
        $this->registry
            ->getOrRegisterCounter('scraper_app','userTierDowngrade','number of times users downgraded their tier')
            ->inc();
    }

}
