<?php

namespace App\patterns;

use App\Entity\AuditLog;
use App\Entity\User;

interface AuditLogBuilder
{
    public function reset();
//    public function withEvent($event);
// Will keep it commented just in case but due to every class that will implement this presetting events the (code) user doesn't need to do it manually
    public function withUser(?User $user);
    public function withDetails(?string $details);
    public function getAuditLog(): AuditLog;
}
