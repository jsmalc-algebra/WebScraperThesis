<?php

namespace App\patterns\AuditLogBuilders;

use App\Enum\AuditEvent;
use App\patterns\AbstractAuditLogBuilder;

class UserRegistrationAuditLogBuilder extends AbstractAuditLogBuilder
{

    protected function event(): AuditEvent
    {
        return AuditEvent::Registration;
    }
}
