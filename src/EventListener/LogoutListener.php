<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final class LogoutListener
{
    #[AsEventListener]
    public function onLogoutEvent(LogoutEvent $event): void
    {
        $user = $event->getToken()->getUser();
        //TODO: get back to this once log patterns are implemented
    }
}
