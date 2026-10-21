<?php

namespace App\patterns;

use App\patterns\AuditLogBuilders\AdminUserCreationAuditLogBuilder;
use App\patterns\AuditLogBuilders\AdminUserDeleteAuditLogBuilder;
use App\patterns\AuditLogBuilders\AdminUserEditAuditLogBuilder;
use App\patterns\AuditLogBuilders\UserLoginAuditLogBuilder;
use App\patterns\AuditLogBuilders\UserLogoutAuditLogBuilder;
use App\patterns\AuditLogBuilders\UserRegistrationAuditLogBuilder;
use App\patterns\AuditLogBuilders\UserScrapeAuditLogBuilder;
use App\patterns\AuditLogBuilders\UserSelfDeleteAuditLogBuilder;
use App\patterns\AuditLogBuilders\UserSelfEditAuditLogBuilder;
use App\patterns\AuditLogBuilders\UserTierChangeAuditLogBuilder;

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
