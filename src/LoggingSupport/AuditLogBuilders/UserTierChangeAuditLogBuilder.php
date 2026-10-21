<?php

namespace App\LoggingSupport\AuditLogBuilders;

use App\Enum\AuditEvent;
use App\LoggingSupport\AbstractAuditLogBuilder;

class UserTierChangeAuditLogBuilder extends AbstractAuditLogBuilder
{

    protected function event(): AuditEvent
    {
        return AuditEvent::TierChange;
    }
}
