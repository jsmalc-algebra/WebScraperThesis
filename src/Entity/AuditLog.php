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
    private ?AuditEvent $Event = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $Timetag = null;

    #[ORM\ManyToOne(inversedBy: 'auditLogs')]
    private ?User $User = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Details = null;

    /**
     * @param AuditEvent|null $Event
     * @param User|null $User
     * @param string|null $Details
     */
    public function __construct(?AuditEvent $Event, ?User $User, ?string $Details)
    {
        $this->Event = $Event;
        $this->Timetag =new \DateTimeImmutable();
        $this->User = $User;
        $this->Details = $Details;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): AuditEvent
    {
        return $this->Event;
    }

    public function setEvent(?AuditEvent $event): static
    {
        $this->Event = $event;

        return $this;
    }

    public function getTimetag(): ?\DateTimeImmutable
    {
        return $this->Timetag;
    }

    public function setTimetag(\DateTimeImmutable $Timetag): static
    {
        $this->Timetag = $Timetag;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getDetails(): ?string
    {
        return $this->Details;
    }

    public function setDetails(?string $Details): static
    {
        $this->Details = $Details;

        return $this;
    }
}
