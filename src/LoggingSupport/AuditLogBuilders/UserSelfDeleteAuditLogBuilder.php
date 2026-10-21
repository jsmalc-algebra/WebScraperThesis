<?php

namespace App\LoggingSupport\AuditLogBuilders;

use App\Enum\AuditEvent;
use App\LoggingSupport\AbstractAuditLogBuilder;

class UserSelfDeleteAuditLogBuilder extends AbstractAuditLogBuilder
{

    protected function event(): AuditEvent
    {
        return AuditEvent::SelfDelete;
    }
}
