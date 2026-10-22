<?php

namespace App\EventListener;

use App\Entity\User;
use App\LoggingSupport\LoggingActions;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

final readonly class AuthenticationAuditListener
{
    public function __construct(private LoggingActions $logger){}

    #[AsEventListener]
    public function onLogoutEvent(LogoutEvent $event): void
    {
        $user = $event->getToken()?->getUser();

        if (!$user instanceof User) {return;} // Might make this an error in the future, tho it should run cleanly

        $this->logger->userLogout($user);
    }

    #[AsEventListener]
    public function onLoginEvent(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {return;} // Ditto

        $this->logger->userLogin($user);
    }
}
