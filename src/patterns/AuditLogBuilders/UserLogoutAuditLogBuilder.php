<?php

namespace App\patterns\AuditLogBuilders;

use App\Enum\AuditEvent;
use App\patterns\AbstractAuditLogBuilder;

class UserLogoutAuditLogBuilder extends AbstractAuditLogBuilder
{

    protected function event(): AuditEvent
    {
       return AuditEvent::Logout;
    }
}
