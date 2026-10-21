<?php

namespace App\Enum;

enum AuditEvent: string
{
    case Registration = 'REGISTRATION';
    case Login = 'LOGIN';
    case Logout = 'LOGOUT';
    case Scrape = 'SCRAPE';
    case SelfEdit = 'SELF-EDIT';
    case TierChange = 'TIER-CHANGE';
    case SelfDelete = 'SELF-DELETE';
    case UserCreation = 'USER-CREATION';
    case AdminEdit = 'ADMIN-EDIT';
    case AdminDelete = 'ADMIN-DELETE';
}
