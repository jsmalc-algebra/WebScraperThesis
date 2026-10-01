<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

final class LanguageListener
{
    #[AsEventListener(event: RequestEvent::class, priority: 20)] # Priority 20 so that it goes before the builtin LocaleListener which would overwrite the session
    public function __invoke(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ($language = $request->getSession()->get('language')) {
            $request->setLocale($language);
        }
    }
}
