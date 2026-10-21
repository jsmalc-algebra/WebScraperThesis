<?php

namespace App\Entity;

use App\Repository\AuditLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuditLogRepository::class)]
class AuditLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Event = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $Timetag = null;

    #[ORM\ManyToOne(inversedBy: 'auditLogs')]
    private ?User $User = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Details = null;

    /**
     * @param \DateTimeImmutable|null $Timetag
     */
    public function __construct()
    {
        $this->Timetag =new \DateTimeImmutable();
    }

    public static function create(): self
    {
        return new self();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): ?string
    {
        return $this->Event;
    }

    public function setEvent(string $Event): static
    {
        $this->Event = $Event;

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
