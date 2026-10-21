<?php

namespace App\LoggingSupport;

use App\Entity\AuditLog;
use App\Entity\User;

interface AuditLogBuilder
{
    public function withUser(?User $user);
    public function withDetails(?string $details);
    public function getAuditLog(): AuditLog;
}
