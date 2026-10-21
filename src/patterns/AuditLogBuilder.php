<?php

namespace App\patterns;

interface AuditLogBuilder
{
    public function reset();
//    public function withEvent($event);
// Will keep it commented just in case but due to every class that will implement this presetting events the (code) user doesn't need to do it manually
    public function withUser($user);
    public function withDetails($details);
}
