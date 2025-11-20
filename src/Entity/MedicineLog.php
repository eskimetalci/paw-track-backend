<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Repository\MedicineLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MedicineLogRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('VIEW', object)"),
        new GetCollection(),
        new Post(),
        new Put(security: "is_granted('EDIT', object)"),
        new Delete(security: "is_granted('DELETE', object)")
    ],
    normalizationContext: ['groups' => ['medicine_log:read']],
    denormalizationContext: ['groups' => ['medicine_log:write']]
)]
class MedicineLog
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['medicine_log:read', 'animal:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'medicineLogs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['medicine_log:read', 'medicine_log:write'])]
    private ?Animal $animal = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[Groups(['medicine_log:read', 'medicine_log:write', 'animal:read'])]
    private ?string $medicineName = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 100)]
    #[Groups(['medicine_log:read', 'medicine_log:write', 'animal:read'])]
    private ?string $dosage = null;

    /**
     * Frequency (e.g., "BID" = twice a day, "QD" = once a day, "PRN" = as needed)
     */
    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 100)]
    #[Groups(['medicine_log:read', 'medicine_log:write', 'animal:read'])]
    private ?string $frequency = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull]
    #[Groups(['medicine_log:read', 'medicine_log:write', 'animal:read'])]
    private ?\DateTimeInterface $startDate = null;

    /**
     * If null, it's a chronic/ongoing medication
     */
    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['medicine_log:read', 'medicine_log:write', 'animal:read'])]
    private ?\DateTimeInterface $endDate = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['medicine_log:read', 'medicine_log:write', 'animal:read'])]
    private ?string $notes = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['medicine_log:read', 'medicine_log:write', 'animal:read'])]
    private ?string $prescribedBy = null;

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

    public function getMedicineName(): ?string
    {
        return $this->medicineName;
    }

    public function setMedicineName(string $medicineName): static
    {
        $this->medicineName = $medicineName;

        return $this;
    }

    public function getDosage(): ?string
    {
        return $this->dosage;
    }

    public function setDosage(string $dosage): static
    {
        $this->dosage = $dosage;

        return $this;
    }

    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    public function setFrequency(string $frequency): static
    {
        $this->frequency = $frequency;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

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

    public function getPrescribedBy(): ?string
    {
        return $this->prescribedBy;
    }

    public function setPrescribedBy(?string $prescribedBy): static
    {
        $this->prescribedBy = $prescribedBy;

        return $this;
    }

    /**
     * Check if medication is currently active
     */
    #[Groups(['medicine_log:read'])]
    public function isActive(): bool
    {
        $now = new \DateTime();
        
        // If there's no end date, it's chronic/ongoing
        if ($this->endDate === null) {
            return $this->startDate <= $now;
        }

        // Check if current date is within the date range
        return $this->startDate <= $now && $now <= $this->endDate;
    }
}

