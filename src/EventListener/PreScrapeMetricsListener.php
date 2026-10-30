<?php

namespace App\EventListener;


use App\PrometheusMetrics\UserNumberMetrics;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::REQUEST, priority: 0)]
readonly class PreScrapeMetricsListener
{
    public function __construct(private UserNumberMetrics $metrics){}

    public function __invoke(RequestEvent $event): void
    {
        if (
            !$event->isMainRequest()
            ||
            $event->getRequest()->attributes->get('_route') !=='prometheus_bundle_prometheus'){
            return;
        }

        $this->metrics->totalUsers();
        $this->metrics->totalUsersPerRole();
    }
}
