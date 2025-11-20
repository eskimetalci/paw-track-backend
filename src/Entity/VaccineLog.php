<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\VaccineLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VaccineLogRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('VIEW', object)"),
        new GetCollection(),
        new Post(),
        new Put(security: "is_granted('EDIT', object)"),
        new Delete(security: "is_granted('DELETE', object)")
    ],
    normalizationContext: ['groups' => ['vaccine_log:read']],
    denormalizationContext: ['groups' => ['vaccine_log:write']]
)]
class VaccineLog
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['vaccine_log:read', 'animal:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'vaccineLogs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['vaccine_log:read', 'vaccine_log:write'])]
    private ?Animal $animal = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[Groups(['vaccine_log:read', 'vaccine_log:write', 'animal:read'])]
    private ?string $vaccineName = null;

    /**
     * Critical for recall tracking
     */
    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['vaccine_log:read', 'vaccine_log:write', 'animal:read'])]
    private ?string $batchNumber = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull]
    #[Assert\LessThanOrEqual('today')]
    #[Groups(['vaccine_log:read', 'vaccine_log:write', 'animal:read'])]
    private ?\DateTimeInterface $administeredAt = null;

    /**
     * Next due date - triggers for frontend notifications
     */
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['vaccine_log:read', 'vaccine_log:write', 'animal:read'])]
    private ?\DateTimeInterface $nextDueDate = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[Groups(['vaccine_log:read', 'vaccine_log:write', 'animal:read'])]
    private ?string $clinicName = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['vaccine_log:read', 'vaccine_log:write', 'animal:read'])]
    private ?string $veterinarianName = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['vaccine_log:read', 'vaccine_log:write', 'animal:read'])]
    private ?string $notes = null;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): static
    {
        $this->animal = $animal;

        return $this;
    }

    public function getVaccineName(): ?string
    {
        return $this->vaccineName;
    }

    public function setVaccineName(string $vaccineName): static
    {
        $this->vaccineName = $vaccineName;

        return $this;
    }

    public function getBatchNumber(): ?string
    {
        return $this->batchNumber;
    }

    public function setBatchNumber(?string $batchNumber): static
    {
        $this->batchNumber = $batchNumber;

        return $this;
    }

    public function getAdministeredAt(): ?\DateTimeInterface
    {
        return $this->administeredAt;
    }

    public function setAdministeredAt(\DateTimeInterface $administeredAt): static
    {
        $this->administeredAt = $administeredAt;

        return $this;
    }

    public function getNextDueDate(): ?\DateTimeInterface
    {
        return $this->nextDueDate;
    }

    public function setNextDueDate(?\DateTimeInterface $nextDueDate): static
    {
        $this->nextDueDate = $nextDueDate;

        return $this;
    }

    public function getClinicName(): ?string
    {
        return $this->clinicName;
    }

    public function setClinicName(string $clinicName): static
    {
        $this->clinicName = $clinicName;

        return $this;
    }

    public function getVeterinarianName(): ?string
    {
        return $this->veterinarianName;
    }

    public function setVeterinarianName(?string $veterinarianName): static
    {
        $this->veterinarianName = $veterinarianName;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Check if vaccine is due for renewal
     */
    #[Groups(['vaccine_log:read'])]
    public function isDue(): bool
    {
        if ($this->nextDueDate === null) {
            return false;
        }

        $now = new \DateTime();
        return $now >= $this->nextDueDate;
    }

    /**
     * Days until next vaccine is due (negative if overdue)
     */
    #[Groups(['vaccine_log:read'])]
    public function getDaysUntilDue(): ?int
    {
        if ($this->nextDueDate === null) {
            return null;
        }

        $now = new \DateTime();
        $interval = $now->diff($this->nextDueDate);
        
        return $interval->invert ? -$interval->days : $interval->days;
    }
}

