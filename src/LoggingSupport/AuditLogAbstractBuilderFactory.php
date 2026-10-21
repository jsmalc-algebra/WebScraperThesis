<?php

namespace App\LoggingSupport;

use App\LoggingSupport\AuditLogBuilders\AdminUserCreationAuditLogBuilder;
use App\LoggingSupport\AuditLogBuilders\AdminUserDeleteAuditLogBuilder;
use App\LoggingSupport\AuditLogBuilders\AdminUserEditAuditLogBuilder;
use App\LoggingSupport\AuditLogBuilders\UserLoginAuditLogBuilder;
use App\LoggingSupport\AuditLogBuilders\UserLogoutAuditLogBuilder;
use App\LoggingSupport\AuditLogBuilders\UserRegistrationAuditLogBuilder;
use App\LoggingSupport\AuditLogBuilders\UserScrapeAuditLogBuilder;
use App\LoggingSupport\AuditLogBuilders\UserSelfDeleteAuditLogBuilder;
use App\LoggingSupport\AuditLogBuilders\UserSelfEditAuditLogBuilder;
use App\LoggingSupport\AuditLogBuilders\UserTierChangeAuditLogBuilder;

class AuditLogAbstractBuilderFactory
{
    public static function createUserRegistrationAuditLogBuilder(): UserRegistrationAuditLogBuilder
    {
        return new UserRegistrationAuditLogBuilder();
    }
    public static function createUserLoginAuditLogBuilder(): UserLoginAuditLogBuilder
    {
        return new UserLoginAuditLogBuilder();
    }
    public static function createUserLogoutAuditLogBuilder(): UserLogoutAuditLogBuilder
    {
        return new UserLogoutAuditLogBuilder();
    }
    public static function createUserScrapeAuditLogBuilder(): UserScrapeAuditLogBuilder
    {
        return new UserScrapeAuditLogBuilder();
    }
    public static function createUserSelfEditAuditLogBuilder(): UserSelfEditAuditLogBuilder
    {
        return new UserSelfEditAuditLogBuilder();
    }
    public static function createUserTierChangeAuditLogBuilder(): UserTierChangeAuditLogBuilder
    {
        return new UserTierChangeAuditLogBuilder();
    }
    public static function createUserSelfDeleteAuditLogBuilder(): UserSelfDeleteAuditLogBuilder
    {
        return new UserSelfDeleteAuditLogBuilder();
    }
    public static function createAdminUserCreationAuditLogBuilder(): AdminUserCreationAuditLogBuilder
    {
        return new AdminUserCreationAuditLogBuilder();
    }
    public static function createAdminUserEditAuditLogBuilder(): AdminUserEditAuditLogBuilder
    {
        return new AdminUserEditAuditLogBuilder();
    }
    public static function createAdminUserDeleteAuditLogBuilder(): AdminUserDeleteAuditLogBuilder
    {
        return new AdminUserDeleteAuditLogBuilder();
    }
}
