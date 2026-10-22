<?php

namespace App\Entity;

use App\Enum\AuditEvent;
use App\Repository\AuditLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuditLogRepository::class)]
class AuditLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, enumType: AuditEvent::class)]
    private ?AuditEvent $event = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $timetag = null;

    #[ORM\ManyToOne(inversedBy: 'auditLogs')]
    private ?User $user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $details = null;

    /**
     * @param AuditEvent|null $event
     * @param User|null $user
     * @param string|null $details
     */
    public function __construct(?AuditEvent $event, ?User $user, ?string $details)
    {
        $this->event = $event;
        $this->timetag =new \DateTimeImmutable();
        $this->user = $user;
        $this->details = $details;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): AuditEvent
    {
        return $this->event;
    }

    public function setEvent(?AuditEvent $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getTimetag(): ?\DateTimeImmutable
    {
        return $this->timetag;
    }

    public function setTimetag(\DateTimeImmutable $timetag): static
    {
        $this->timetag = $timetag;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(?string $details): static
    {
        $this->details = $details;

        return $this;
    }
}
