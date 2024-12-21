<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\UtilisateursRepository;
use App\State\ChangeRoleProcessor;
use App\State\DeleteUserProcessor;
use App\State\UtilisateursProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('login', message: 'Ce login est déjà utilisé!')]
#[UniqueEntity('email', message: 'Cette adresse email est déjà utilisée!')]
#[ORM\Entity(repositoryClass: UtilisateursRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_LOGIN', fields: ['login'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
        new GetCollection(description: 'Récupère les utilisateurs', security: 'is_granted("ROLE_ADMIN")'),
        new Get(description: 'Récupère un utilisateur', security: '(is_granted("ROLE_USER") and object.getOwner() == user) or is_granted("ROLE_ADMIN")'),
        new Delete(description: 'Supprime un utilisateur', security: '(is_granted("ROLE_USER") and object.getOwner() == user) or is_granted("ROLE_ADMIN")', processor:DeleteUserProcessor::class),
        new Patch(description: 'Met à jour un utilisateur', normalizationContext: ['groups' => ['utilisateur:update']], denormalizationContext: ['groups' => ['utilisateur:update']],
            security: '(is_granted("ROLE_USER") and object.getOwner() == user) or is_granted("ROLE_ADMIN")',
            validationContext: ['groups' => ['Default', 'utilisateur:update']], processor: UtilisateursProcessor::class),
        new Patch(uriTemplate: '/utilisateurs/{id}/changeRole', description: "Changer le role d'un utilisateur", denormalizationContext: ['groups' => ['utilisateur:read:role']], security: 'is_granted("ROLE_ADMIN") and is_granted("CHANGER_ROLES", object)', processor: ChangeRoleProcessor::class),
        new Post(uriTemplate: '/auth/register', inputFormats: ['json' => ['application/json']], description: 'Crée un utilisateur', normalizationContext: ['groups' => ['utilisateur:none']], denormalizationContext: ['groups' => ['utilisateur:register']], validationContext: ['groups' => ['Default', 'utilisateur:register']], output: false, processor: UtilisateursProcessor::class),
    ],
    normalizationContext: ['groups' => ['allutilisateur:read']],
    denormalizationContext: ['groups' => ['allutilisateur:write']],
    validationContext: ['groups' => ['Default', 'allutilisateur:write']],
)]
#[ORM\HasLifecycleCallbacks]
class Utilisateurs implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['infoparticipation:read', 'updateutilisateur:read', 'participation:event:read', 'utilisateur:read', 'participation:event:read', 'participation:utilisateur:read', 'allutilisateur:read', 'infoutilisateur:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(groups: ['utilisateur:register'])]
    #[Assert\Length(min: 4, max: 20, minMessage: 'Il faut au moins 4 caractères pour le login!', maxMessage: 'Il faut moins de 20 caractères pour le login!')]
    #[Groups(['infoparticipation:read', 'updateutilisateur:read', 'utilisateur:register', 'utilisateur:read', 'participation:event:read', 'participation:utilisateur:read', 'participation:utilisateur:event:read', 'allutilisateur:read', 'infoutilisateur:read'])]
    private ?string $login = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['infoparticipation:read', 'updateutilisateur:read', 'utilisateur:update', 'utilisateur:read', 'participation:event:read', 'participation:utilisateur:read', 'participation:utilisateur:event:read', 'allutilisateur:read', 'infoutilisateur:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['infoparticipation:read', 'updateutilisateur:read', 'utilisateur:update', 'utilisateur:read', 'participation:event:read', 'participation:utilisateur:read', 'participation:utilisateur:event:read', 'allutilisateur:read', 'infoutilisateur:read'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['infoparticipation:read', 'updateutilisateur:read', 'utilisateur:update', 'utilisateur:read', 'participation:event:read', 'participation:utilisateur:read', 'participation:utilisateur:event:read', 'allutilisateur:read', 'infoutilisateur:read'])]
    private ?string $telephone = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(groups: ['utilisateur:register'])]
    #[Assert\NotNull(groups: ['utilisateur:register'])]
    #[Assert\Email(message: 'L\'adresse email n\'est pas valide')]
    #[Groups(['infoparticipation:read', 'updateutilisateur:read', 'utilisateur:register', 'utilisateur:update', 'utilisateur:read', 'participation:event:read', 'participation:utilisateur:read', 'participation:utilisateur:event:read', 'allutilisateur:read', 'infoutilisateur:read'])]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $hash = null;

    #[ORM\Column]
    #[Groups(['infoparticipation:read', 'updateutilisateur:read', 'utilisateur:register', 'utilisateur:update', 'utilisateur:read', 'participation:event:read', 'utilisateur:read:role', 'allutilisateur:read', 'infoutilisateur:read'])]
    private array $roles = [];

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updated_at = null;

    #[Assert\NotBlank(message: "Le mot de passe ne peut pas être vide.", groups: ['utilisateur:register'])]
    #[Assert\NotNull(groups: ['utilisateur:register'])]
    #[Assert\Length(min: 8, max: 30, minMessage: "Il faut au moins 8 caractères pour le mdp!", maxMessage: "Il faut moins de 30 caractères pour le mdp!", groups: ['utilisateur:create'])]
    #[Assert\Regex(pattern: '#^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d\w\W]{8,30}$#', message: 'Le mot de passe doit contenir au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial', groups: ['utilisateur:create'])]
    #[Groups(['utilisateur:register', 'utilisateur:update', 'utilisateur:read', 'participation:event:read'])]
    private ?string $plainPassword = null;

    #[ORM\OneToMany(targetEntity: Events::class, mappedBy: 'organized_by', cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['updateutilisateur:read', 'utilisateur:read', 'allutilisateur:read', 'infoutilisateur:read'])]
    private Collection $events;

    #[ORM\OneToMany(targetEntity: Participations::class, mappedBy: 'user', cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['updateutilisateur:read', 'utilisateur:read', 'allutilisateur:read', 'infoutilisateur:read'])]
    private Collection $participations;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->participations = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updated_at = new \DateTime();
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): void
    {
        $this->login = $login;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): static
    {
        $this->hash = $hash;
        return $this;
    }

    public function removeRole($role): void
    {
        $index = array_search($role, $this->roles);
        if ($index !== false) {
            unset($this->roles[$index]);
        }
    }

    public function addRole($role): void
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
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

    public function getPassword(): ?string
    {
        return $this->hash;
    }

    public function getUserIdentifier(): string
    {
        return $this->login;
    }

    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Events $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setOrganizedBy($this);
        }
        return $this;
    }

    public function removeEvent(Events $event): static
    {
        if ($this->events->removeElement($event) && $event->getOrganizedBy() === $this) {
            $event->setOrganizedBy(null);
        }
        return $this;
    }

    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipations(Participations $participations): static
    {
        if (!$this->participations->contains($participations)) {
            $this->participations->add($participations);
            $participations->setUserId($this);
        }
        return $this;
    }

    public function removeParticipations(Participations $participations): static
    {
        if ($this->participations->removeElement($participations) && $participations->getUserId() === $this) {
            $participations->setUserId(null);
        }
        return $this;
    }

    public function getOwner(): ?Utilisateurs
    {
        return $this;
    }
}