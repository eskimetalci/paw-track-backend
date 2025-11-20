<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Enum\PooColor;
use App\Enum\PooContent;
use App\Repository\PooLogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PooLogRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('VIEW', object)"),
        new GetCollection(),
        new Post(),
        new Put(security: "is_granted('EDIT', object)"),
        new Delete(security: "is_granted('DELETE', object)")
    ],
    normalizationContext: ['groups' => ['poo_log:read']],
    denormalizationContext: ['groups' => ['poo_log:write']]
)]
class PooLog
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['poo_log:read', 'animal:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'pooLogs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['poo_log:read', 'poo_log:write'])]
    private ?Animal $animal = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotNull]
    #[Assert\LessThanOrEqual('now')]
    #[Groups(['poo_log:read', 'poo_log:write', 'animal:read'])]
    private ?\DateTimeInterface $recordedAt = null;

    /**
     * Bristol Stool Scale: 1-7
     * 1-2: Constipation
     * 3-4: Healthy/Normal
     * 5-7: Diarrhea
     */
    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull]
    #[Assert\Range(min: 1, max: 7)]
    #[Groups(['poo_log:read', 'poo_log:write', 'animal:read'])]
    private ?int $bristolScore = null;

    #[ORM\Column(enumType: PooColor::class)]
    #[Assert\NotNull]
    #[Groups(['poo_log:read', 'poo_log:write', 'animal:read'])]
    private ?PooColor $color = null;

    /**
     * Array of PooContent enum values stored as JSON
     */
    #[ORM\Column(type: Types::JSON)]
    #[Assert\NotNull]
    #[Groups(['poo_log:read', 'poo_log:write', 'animal:read'])]
    private array $contents = [];

    #[ORM\Column(length: 500, nullable: true)]
    #[Assert\Url]
    #[Groups(['poo_log:read', 'poo_log:write', 'animal:read'])]
    private ?string $photoUrl = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['poo_log:read', 'poo_log:write', 'animal:read'])]
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

    public function getRecordedAt(): ?\DateTimeInterface
    {
        return $this->recordedAt;
    }

    public function setRecordedAt(\DateTimeInterface $recordedAt): static
    {
        $this->recordedAt = $recordedAt;

        return $this;
    }

    public function getBristolScore(): ?int
    {
        return $this->bristolScore;
    }

    public function setBristolScore(int $bristolScore): static
    {
        $this->bristolScore = $bristolScore;

        return $this;
    }

    public function getColor(): ?PooColor
    {
        return $this->color;
    }

    public function setColor(PooColor $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getContents(): array
    {
        return $this->contents;
    }

    public function setContents(array $contents): static
    {
        $this->contents = $contents;

        return $this;
    }

    public function getPhotoUrl(): ?string
    {
        return $this->photoUrl;
    }

    public function setPhotoUrl(?string $photoUrl): static
    {
        $this->photoUrl = $photoUrl;

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
     * Get health zone based on Bristol Score
     * 1-2: Constipation, 3-4: Healthy, 5-7: Diarrhea
     */
    #[Groups(['poo_log:read'])]
    public function getHealthZone(): string
    {
        return match (true) {
            $this->bristolScore >= 1 && $this->bristolScore <= 2 => 'CONSTIPATION',
            $this->bristolScore >= 3 && $this->bristolScore <= 4 => 'HEALTHY',
            $this->bristolScore >= 5 && $this->bristolScore <= 7 => 'DIARRHEA',
            default => 'UNKNOWN',
        };
    }
}

