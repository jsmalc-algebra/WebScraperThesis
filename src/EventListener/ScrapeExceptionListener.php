<?php

namespace App\EventListener;


use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface as HttpClientException;

/**
 * One place that turns "scrape not possible" exceptions into
 * "flash message + back to the input page". Controllers stay free of try/catch.
 */
#[AsEventListener(event: ExceptionEvent::class)]
final class ScrapeExceptionListener
{
    public function __construct(private UrlGeneratorInterface $urls) {}

    public function __invoke(ExceptionEvent $event): void
    {
        $error = $event->getThrowable();

        if ($error instanceof HttpClientException) {
            $message = 'Could not fetch that page.';
        } else {
            return;   // not ours: let Symfony handle it normally
        }

        $session = $event->getRequest()->getSession();

        if ($session instanceof FlashBagAwareSessionInterface) {
            $session->getFlashBag()->add('error', $message);
        }

        $event->setResponse(new RedirectResponse(
            $this->urls->generate('app_scrape_input')   // your input page's route name
        ));
    }
}
