<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\EventsRepository;
use App\State\AddCountsToEvents;
use App\State\AddInfoToEvent;
use App\State\EventsProcessor;
use App\State\GetInfoParticipationProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Flex\Unpack\Operation;

#[ApiResource(
    operations: [
        new GetCollection(
            description: "Récupère les events",
            normalizationContext: ['groups' => ['event:readInfo', 'event:read', 'event:readCount', 'infoparticipation:read']],
            denormalizationContext: ['groups' => ['event:write']],
            validationContext: ['groups' => ['Default', 'event:write', 'event:read', 'infoparticipation:read']],
            provider: AddCountsToEvents::class,
        ),
        new GetCollection(
            uriTemplate: '/utilisateurs/{idOrga}/events',
            uriVariables: [
                'idOrga' => new Link(
                    fromProperty: 'events',
                    fromClass: Utilisateurs::class
                )
            ],
            description: "Récupère les events d'un organisateur",
            security: "is_granted('ROLE_ORGA') or is_granted('ROLE_ADMIN')",
        ),
        new Get(
            uriTemplate: '/events/{id}',
            uriVariables: [
                'id' => new Link(
                    fromClass: Events::class
                )
            ],
            description: "Récupère un event",
            normalizationContext: ['groups' => ['event:details', 'event:read', 'participation:utilisateur:read']],
            denormalizationContext: ['groups' => ['event:write']],
            provider: AddInfoToEvent::class,
        ),
        new Post(
            description: "Crée un event",
            denormalizationContext: ['groups' => ['event:create']],
            security: "is_granted('ROLE_ORGA')",
            processor: EventsProcessor::class,
        ),
        new Patch(
            description: "Met à jour un event",
            denormalizationContext: ['groups' => ['event:update']],
            security: "(is_granted('ROLE_ORGA') and object.getOwner() == user) or is_granted('ROLE_ADMIN')",
        ),
        new Delete(
            description: "Supprime un event",
            security: "(is_granted('ROLE_ORGA') and object.getOwner() == user) or is_granted('ROLE_ADMIN')",
        )
    ],
    normalizationContext: ['groups' => ['event:read', 'participation:utilisateur:read']],
    denormalizationContext: ['groups' => ['event:write']],
)]
#[ORM\Entity(repositoryClass: EventsRepository::class)]
class Events
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['infoparticipation:read', 'event:readinfo','event:read', 'participation:utilisateur:read', 'participation:event:read','participation:utilisateur:event:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['infoparticipation:read','event:readinfo','event:create', 'event:update','event:write', 'event:read', 'participation:utilisateur:read','participation:utilisateur:event:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['infoparticipation:read','event:readinfo','event:create', 'event:update','event:write', 'event:read', 'participation:utilisateur:read','participation:utilisateur:event:read'])]
    private ?\DateTimeInterface $start_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['infoparticipation:read','event:readinfo','event:create', 'event:update','event:write', 'event:read', 'participation:utilisateur:read','participation:utilisateur:event:read'])]
    private ?\DateTimeInterface $end_at = null;

    #[ORM\Column(type: Types::SIMPLE_ARRAY)]
    #[Groups(['infoparticipation:read','event:readinfo','event:create', 'event:update','event:write', 'event:read', 'participation:utilisateur:read','participation:utilisateur:event:read'])]
    private array $sport = [];

    #[ORM\Column(length: 255)]
    #[Groups(['infoparticipation:read','event:readinfo','event:create', 'event:update','event:write', 'event:read', 'participation:utilisateur:read','participation:utilisateur:event:read'])]
    private ?string $localisation = null;

    #[ORM\Column(length: 255)]
    #[Groups(['infoparticipation:read','event:readinfo','event:create', 'event:update', 'event:write', 'event:read', 'participation:utilisateur:read','participation:utilisateur:event:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['infoparticipation:read','event:readinfo','event:create', 'event:update', 'event:write', 'event:read', 'participation:utilisateur:read','participation:utilisateur:event:read'])]
    private ?int $max_participants = null;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    #[Groups(['event:create', 'event:update','event:readinfo','event:write', 'event:read', 'participation:utilisateur:read'])]
    private ?bool $participants_visible = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['infoparticipation:read','event:readinfo','event:create', 'event:update','event:write', 'event:read', 'participation:utilisateur:read','participation:utilisateur:event:read'])]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'events')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['infoparticipation:read','event:readinfo','event:read', 'event:create', 'event:write', 'event:read', 'participation:utilisateur:read','participation:utilisateur:event:read'])]
    #[ApiProperty(readable: true, writable: false)]
    private ?Utilisateurs $organized_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['event:write', 'event:read','event:readinfo', 'participation:utilisateur:read'])]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['event:write', 'event:read','event:readinfo', 'participation:utilisateur:read'])]
    private ?\DateTimeInterface $updated_at = null;

    /**
     * @var Collection<int, Participations>
     */
    #[ORM\OneToMany(targetEntity: Participations::class, mappedBy: 'event', orphanRemoval: true)]
    #[Groups(['event:read'])]
    private Collection $participations;

    #[Groups(['event:details', 'event:write' ,'participation:utilisateur:event:write', 'participation:utilisateur:read', 'participation:utilisateur:event:read'])]
    private bool $participating = false;

    #[Groups(['event:details', 'event:write' ,'participation:utilisateur:event:write', 'participation:utilisateur:read', 'event:readCount', 'participation:utilisateur:event:read'])]
    private int $countOfParticipation = 0;

    public function isCountOfParticipation(): int
    {
        return $this->countOfParticipation;
    }

    public function setCountOfParticipation(int $countOfParticipation): void
    {
        $this->countOfParticipation = $countOfParticipation;
    }

    public function getValueOfChecker(): string
    {
        return $this->valueOfChecker;
    }

    public function setValueOfChecker(string $valueOfChecker): void
    {
        $this->valueOfChecker = $valueOfChecker;
    }

    public function __construct()
    {
        $this->participations = new ArrayCollection();
    }

    public function isParticipating(): bool
    {
        return $this->participating;
    }

    public function setParticipating(bool $participating): void
    {
        $this->participating = $participating;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

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

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->start_at;
    }

    public function setStartAt(\DateTimeInterface $start_at): static
    {
        $this->start_at = $start_at;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->end_at;
    }

    public function setEndAt(\DateTimeInterface $end_at): static
    {
        $this->end_at = $end_at;

        return $this;
    }

    public function getSport(): array
    {
        return $this->sport;
    }

    public function setSport(array $sport): static
    {
        $this->sport = $sport;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): static
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getMaxParticipants(): ?int
    {
        return $this->max_participants;
    }

    public function setMaxParticipants(int $max_participants): static
    {
        $this->max_participants = $max_participants;

        return $this;
    }

    public function isParticipantsVisible(): ?bool
    {
        return $this->participants_visible;
    }

    public function setParticipantsVisible(bool $participants_visible): static
    {
        $this->participants_visible = $participants_visible;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getOrganizedBy(): ?Utilisateurs
    {
        return $this->organized_by;
    }

    public function setOrganizedBy(?Utilisateurs $organized_by): static
    {
        $this->organized_by = $organized_by;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, Participations>
     */
    #[Groups(['event:read','participation:utilisateur:read'])]
    public function getParticipations(): Collection
    {
        if ($this->participants_visible) {
            return $this->participations;
        }

        return new ArrayCollection();
    }

    public function addParticipations(Participations $participations): static
    {
        if (!$this->participations->contains($participations)) {
            $this->participations->add($participations);
            $participations->setEventId($this);
        }

        return $this;
    }

    public function removeParticipations(Participations $participations): static
    {
        if ($this->participations->removeElement($participations)) {
            // set the owning side to null (unless already changed)
            if ($participations->getEventId() === $this) {
                $participations->setEventId(null);
            }
        }

        return $this;
    }

    public function getOwner(): ?Utilisateurs
    {
        return $this->getOrganizedBy();
    }

}