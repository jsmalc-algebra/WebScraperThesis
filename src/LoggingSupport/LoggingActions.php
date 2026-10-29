<?php

namespace App\LoggingSupport;

use App\Entity\AuditLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

readonly class LoggingActions implements LoggableActionsInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private Security $security,
    ){}

    private function logEvent(AuditLog $auditLog): void
    {
        $this->entityManager->persist($auditLog);
        $this->entityManager->flush();
    }

    public function userRegistration(User $user): void
    {
        $this->logEvent(
            AuditLogAbstractBuilderFactory::createUserRegistrationAuditLogBuilder()
            ->withUser($user)
            ->getAuditLog()
        );
    }

    public function userLogin(User $user): void
    {
        $this->logEvent(
            AuditLogAbstractBuilderFactory::createUserLoginAuditLogBuilder()
            ->withUser($user)
            ->getAuditLog()
        );
    }

    public function userLogout(User $user): void
    {
        $this->logEvent(
            AuditLogAbstractBuilderFactory::createUserLogoutAuditLogBuilder()
            ->withUser($user)
            ->getAuditLog()
        );
    }

    public function userScrape(string $details): void
    {
        $this->logEvent(
            AuditLogAbstractBuilderFactory::createUserScrapeAuditLogBuilder()
            ->withDetails($details)
            ->withUser($this->security->getUser())
            ->getAuditLog()
        );
    }

    public function userSelfEdit(string $details): void
    {
        $this->logEvent(
            AuditLogAbstractBuilderFactory::createUserSelfEditAuditLogBuilder()
                ->withDetails($details)
                ->withUser($this->security->getUser())
                ->getAuditLog()
        );
    }

    public function userTierChange(string $details): void
    {
        $this->logEvent(
            AuditLogAbstractBuilderFactory::createUserTierChangeAuditLogBuilder()
                ->withDetails($details)
                ->withUser($this->security->getUser())
                ->getAuditLog()
        );
    }

    public function userSelfDelete(string $details): void
    {
        $this->logEvent(
            AuditLogAbstractBuilderFactory::createUserSelfDeleteAuditLogBuilder()
                ->withUser($this->security->getUser())
                ->withDetails($details)
                ->getAuditLog()
        );
    }

    public function adminUserCreation(string $details): void
    {
        $this->logEvent(
            AuditLogAbstractBuilderFactory::createAdminUserCreationAuditLogBuilder()
                ->withDetails($details)
                ->withUser($this->security->getUser())
                ->getAuditLog()
        );
    }

    public function adminUserEdit(string $details): void
    {
        $this->logEvent(
            AuditLogAbstractBuilderFactory::createAdminUserEditAuditLogBuilder()
            ->withDetails($details)
            ->withUser($this->security->getUser())
            ->getAuditLog()
        );
    }

    public function adminUserDelete(string $details): void
    {
        $this->logEvent(
            AuditLogAbstractBuilderFactory::createAdminUserDeleteAuditLogBuilder()
            ->withUser($this->security->getUser())
            ->withDetails($details)
            ->getAuditLog()
        );
    }
}
