<?php

namespace App\patterns\AuditLogs;

use App\Entity\AuditLog;
use App\patterns\AuditLogBuilder;


class UserRegistrationAuditLog extends AuditLog implements AuditLogBuilder
{
    private AuditLog $auditLog;

    /**
     * @param AuditLog $auditLog
     */
    public function __construct()
    {

    }

    public function getAuditLog(): AuditLog
    {
        $new_log = $this->auditLog;
        $this->reset();
        return $new_log;
    }



    public function reset()
    {
        // TODO: Implement reset() method.
    }

    public function withEvent($event)
    {
        // TODO: Implement withEvent() method.
    }

    public function withUser($user)
    {
        // TODO: Implement withUser() method.
    }

    public function withDetails($details)
    {
        // TODO: Implement withDetails() method.
    }
}
