<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Enum\Species;
use App\Repository\AnimalRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AnimalRepository::class)]
#[ApiResource(
    operations: [
        new Get(security: "is_granted('VIEW', object)"),
        new GetCollection(),
        new Post(),
        new Put(security: "is_granted('EDIT', object)"),
        new Delete(security: "is_granted('DELETE', object)")
    ],
    normalizationContext: ['groups' => ['animal:read']],
    denormalizationContext: ['groups' => ['animal:write']]
)]
class Animal
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['animal:read', 'user:read'])]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'animals')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['animal:read', 'animal:write'])]
    private ?User $owner = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    #[Groups(['animal:read', 'animal:write', 'user:read'])]
    private ?string $name = null;

    #[ORM\Column(enumType: Species::class)]
    #[Assert\NotNull]
    #[Groups(['animal:read', 'animal:write', 'user:read'])]
    private ?Species $species = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255)]
    #[Groups(['animal:read', 'animal:write', 'user:read'])]
    private ?string $breed = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotNull]
    #[Assert\LessThanOrEqual('today')]
    #[Groups(['animal:read', 'animal:write', 'user:read'])]
    private ?\DateTimeInterface $dob = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Positive]
    #[Groups(['animal:read', 'animal:write', 'user:read'])]
    private ?float $weight = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Assert\Url]
    #[Groups(['animal:read', 'animal:write', 'user:read'])]
    private ?string $avatar = null;

    #[ORM\OneToMany(targetEntity: PooLog::class, mappedBy: 'animal', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['animal:read'])]
    private Collection $pooLogs;

    #[ORM\OneToMany(targetEntity: MedicineLog::class, mappedBy: 'animal', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['animal:read'])]
    private Collection $medicineLogs;

    #[ORM\OneToMany(targetEntity: VaccineLog::class, mappedBy: 'animal', cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['animal:read'])]
    private Collection $vaccineLogs;

    public function __construct()
    {
        $this->pooLogs = new ArrayCollection();
        $this->medicineLogs = new ArrayCollection();
        $this->vaccineLogs = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSpecies(): ?Species
    {
        return $this->species;
    }

    public function setSpecies(Species $species): static
    {
        $this->species = $species;

        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(?string $breed): static
    {
        $this->breed = $breed;

        return $this;
    }

    public function getDob(): ?\DateTimeInterface
    {
        return $this->dob;
    }

    public function setDob(\DateTimeInterface $dob): static
    {
        $this->dob = $dob;

        return $this;
    }

    /**
     * Calculate age dynamically from date of birth
     */
    #[Groups(['animal:read'])]
    public function getAge(): ?int
    {
        if ($this->dob === null) {
            return null;
        }

        $now = new \DateTime();
        return $now->diff($this->dob)->y;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, PooLog>
     */
    public function getPooLogs(): Collection
    {
        return $this->pooLogs;
    }

    public function addPooLog(PooLog $pooLog): static
    {
        if (!$this->pooLogs->contains($pooLog)) {
            $this->pooLogs->add($pooLog);
            $pooLog->setAnimal($this);
        }

        return $this;
    }

    public function removePooLog(PooLog $pooLog): static
    {
        if ($this->pooLogs->removeElement($pooLog)) {
            if ($pooLog->getAnimal() === $this) {
                $pooLog->setAnimal(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MedicineLog>
     */
    public function getMedicineLogs(): Collection
    {
        return $this->medicineLogs;
    }

    public function addMedicineLog(MedicineLog $medicineLog): static
    {
        if (!$this->medicineLogs->contains($medicineLog)) {
            $this->medicineLogs->add($medicineLog);
            $medicineLog->setAnimal($this);
        }

        return $this;
    }

    public function removeMedicineLog(MedicineLog $medicineLog): static
    {
        if ($this->medicineLogs->removeElement($medicineLog)) {
            if ($medicineLog->getAnimal() === $this) {
                $medicineLog->setAnimal(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, VaccineLog>
     */
    public function getVaccineLogs(): Collection
    {
        return $this->vaccineLogs;
    }

    public function addVaccineLog(VaccineLog $vaccineLog): static
    {
        if (!$this->vaccineLogs->contains($vaccineLog)) {
            $this->vaccineLogs->add($vaccineLog);
            $vaccineLog->setAnimal($this);
        }

        return $this;
    }

    public function removeVaccineLog(VaccineLog $vaccineLog): static
    {
        if ($this->vaccineLogs->removeElement($vaccineLog)) {
            if ($vaccineLog->getAnimal() === $this) {
                $vaccineLog->setAnimal(null);
            }
        }

        return $this;
    }
}

