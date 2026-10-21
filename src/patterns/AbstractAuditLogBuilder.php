<?php

namespace App\patterns;

use App\Entity\AuditLog;
use App\Entity\User;
use App\Enum\AuditEvent;
use App\patterns\AuditLogBuilder;

abstract class AbstractAuditLogBuilder implements AuditLogBuilder
{

    protected ?User $user = null;
    protected ?string $details = null;

    abstract protected function event(): AuditEvent;

    public function reset(): static
    {
        $this->user = null;
        $this->details = null;
        return $this;
    }

    public function withUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function withDetails(?string $details): static
    {
        $this->details = $details;
        return $this;
    }

    public function getAuditLog(): AuditLog
    {
        $log = new AuditLog(
            $this->event(),
            $this->user,
            $this->details,
        );
        $this->reset();
        return $log;
    }
}
