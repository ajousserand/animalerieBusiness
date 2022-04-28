<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\AddressRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
#[ApiResource(
    collectionOperations:['get','post'],
    itemOperations:['get'],
    normalizationContext:['groups'=>['address']]
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'streetNumber' => 'exact', 'streetName' => 'partial'])]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['address','command','city'])]
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\Positive(message:"Le numéro de la rue doit être positif"),
    Assert\Type(type:"integer", message:"Le numero de la rue doit être un chiffre entier")
    ]
    #[Groups(['user','address','command','city'])]
    private $streetNumber;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Le nom de la rue ne doit pas être vide"),
      Assert\NotNull(message:"Le nom de la rue ne doit pas être nulle"),
      Assert\Length(min:5,max:100, minMessage:"Le nom de rue doit être plus long",maxMessage:"Le nom de rue doit être plus court"),
      Assert\Type(type:"string", message:"Le numero de rue doit être une chaine de caractère")
      ]
      #[Groups(['user','address','command','city'])] 
    private $streetName;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'addresses')]
    #[Groups(['address'])]
    private $users;

    #[ORM\ManyToOne(targetEntity: City::class, inversedBy: 'addresses')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['address'])]
    private $city;

    #[ORM\OneToMany(mappedBy: 'address', targetEntity: Command::class)]
    private $commands;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->commands = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStreetNumber(): ?int
    {
        return $this->streetNumber;
    }

    public function setStreetNumber(?int $streetNumber): self
    {
        $this->streetNumber = $streetNumber;

        return $this;
    }

    public function getStreetName(): ?string
    {
        return $this->streetName;
    }

    public function setStreetName(string $streetName): self
    {
        $this->streetName = $streetName;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

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
            $command->setAddress($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getAddress() === $this) {
                $command->setAddress(null);
            }
        }

        return $this;
    }
}
