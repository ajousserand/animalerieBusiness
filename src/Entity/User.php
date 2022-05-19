<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CountCustomerController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    collectionOperations:['get','post',
    'get_count_user_from_dates' => [
        'method'=>'GET',
        'path'=> '/user/get_count_user',
        'controller'=> CountCustomerController::class
    ]],
    itemOperations:['get'],
    normalizationContext:['groups'=>['user']]
)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user','review','command'])]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message:"L'email' ne doit pas être vide"),
      Assert\NotNull(message:"L'email' ne doit pas être nulle"),
      Assert\Email(message:"L'email n'est pas du bon type"),
      Assert\Type(type:"string", message:"L'email doit être une chaine de caractère")
      ]
    #[Groups(['user'])]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Le prénom doit pas être vide"),
      Assert\NotNull(message:"Le prénom doit pas être nul"),
      Assert\Length(min:5,max:100, minMessage:"Le prénom doit être plus long",maxMessage:"Le prénom doit être plus court"),
      Assert\Type(type:"string", message:"Le prénom doit être une chaine de caractère")
      ]
    #[Groups(['user','review','address','command'])] 
    private $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Le nom ne doit pas être vide"),
      Assert\NotNull(message:"Le nom ne doit pas être nul"),
      Assert\Length(min:5,max:100, minMessage:"Le nom doit être plus long",maxMessage:"Le nom doit être plus court"),
      Assert\Type(type:"string", message:"Le nom doit être une chaine de caractère")
      ] 
    #[Groups(['user','review','address','command'])]
    private $lastName;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Review::class, cascade:['remove','persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user'])]
    private $reviews;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Command::class)]
    #[Groups(['user'])]
    #[ORM\JoinColumn(nullable: true)]
    private $commands;

    #[ORM\ManyToMany(targetEntity: Address::class, mappedBy: 'users')]
    #[Groups(['user'])]
    #[ORM\JoinColumn(nullable: true)]
    private $addresses;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Le genre ne doit pas être vide"),
      Assert\NotNull(message:"Le genre ne doit pas être nulle"),
      Assert\Length(min:5,max:100, minMessage:"Le genre doit être plus long",maxMessage:"Le genre doit être plus court"),
      Assert\Type(type:"string", message:"Le genre doit être une chaine de caractère")
      ] 
      #[Groups(['user'])]
    private $genre;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message:"La date de naissance ne peut être nulle"),
      Assert\NotBlank(message:"La date de naissance ne peut être vide"),
      Assert\Type(type: 'DateTime',message:"La date de naissance doit comporter une date"),
      Assert\GreaterThanOrEqual(value: '-100 years',message:"La date de naissance est trop petite"),
      Assert\LessThanOrEqual(value: '-18 years',message:"La date de naissance est est trop grande")
    ]
    #[Groups(['user'])]
    private $birthDate;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message:"La date de création ne peut être nulle"),
      Assert\NotBlank(message:"La date de création ne peut être vide"),
      Assert\Type(type: 'DateTime',message:"La date de création doit comporter une date"),
      Assert\GreaterThanOrEqual(value: '-10 years',message:"La date de création est trop petite"),
      Assert\LessThanOrEqual(value: 'today',message:"La date de création est est trop grand")
    ]
    #[Groups(['user'])]
    private $createdAt;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ResetPassword::class, cascade:['remove','persist'])]
    private $resetPasswords;

    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->commands = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->resetPasswords = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setUser($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getUser() === $this) {
                $review->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Command>
     */
    public function getCommands(): Collection
    {
        return $this->commands;
    }

    public function addCommand(Command $command): self
    {
        if (!$this->commands->contains($command)) {
            $this->commands[] = $command;
            $command->setUser($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getUser() === $this) {
                $command->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Address>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Address $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->addUser($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->removeElement($address)) {
            $address->removeUser($this);
        }

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @return Collection<int, ResetPassword>
     */
    public function getResetPasswords(): Collection
    {
        return $this->resetPasswords;
    }

    public function addResetPassword(ResetPassword $resetPassword): self
    {
        if (!$this->resetPasswords->contains($resetPassword)) {
            $this->resetPasswords[] = $resetPassword;
            $resetPassword->setUser($this);
        }

        return $this;
    }

    public function removeResetPassword(ResetPassword $resetPassword): self
    {
        if ($this->resetPasswords->removeElement($resetPassword)) {
            // set the owning side to null (unless already changed)
            if ($resetPassword->getUser() === $this) {
                $resetPassword->setUser(null);
            }
        }

        return $this;
    }

}
