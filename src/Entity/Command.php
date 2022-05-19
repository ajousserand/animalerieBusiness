<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\ConversionCommandController;
use App\Controller\ConversionPanierController;
use App\Controller\CountCommandController;
use App\Controller\CountPanierController;
use App\Controller\PanierAbandonnerController;
use App\Controller\RecurrenceClientController;
use App\Controller\TotalPanierMoyenController;
use App\Controller\TotalProductCountController;

#[ORM\Entity(repositoryClass: CommandRepository::class)]
#[ApiResource(
    collectionOperations:['get','post',
        'get_total_command_from_dates' => [
            'method'=>'GET',
            'path'=> '/command/get_total_command',
            'controller'=> TotalProductCountController::class
        ],
        'get_count_command_from_dates' => [
            'method'=>'GET',
            'path'=> '/command/get_count_command',
            'controller'=> CountCommandController::class
        ],
        'get_count_panier_from_dates' => [
            'method'=>'GET',
            'path'=> '/command/get_count_panier',
            'controller'=> CountPanierController::class
        ],
        'get_total_panier_from_dates' => [
            'method'=>'GET',
            'path'=> '/command/get_total_panier',
            'controller'=> TotalPanierMoyenController::class
        ],
        'get_conversion_panier_from_dates' => [
            'method'=>'GET',
            'path'=> '/command/get_conversion_panier',
            'controller'=> ConversionPanierController::class
        ],
        'get_conversion_command_from_dates' => [
            'method'=>'GET',
            'path'=> '/command/get_conversion_command',
            'controller'=> ConversionCommandController::class
        ],
        'get_recurrence_command_from_dates' => [
            'method'=>'GET',
            'path'=> '/command/get_recurrence_command',
            'controller'=> RecurrenceClientController::class
        ],
        'get_panier_abandonner_from_dates' => [
            'method'=>'GET',
            'path'=> '/command/get_panier_abandonner',
            'controller'=> PanierAbandonnerController::class
        ]

],
    itemOperations:['get'],
    normalizationContext:['groups'=>['command']]
)]
#[ApiFilter(DateFilter::class, properties: ['createdAt'])]
#[ApiFilter(SearchFilter::class, properties: ['user.firstName'=>'exact'])]
class Command
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['command','product'])]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull(message:"Le prix total ne doit pas être nulle"),
      Assert\NotBlank(message:"Le prix total ne doit pas être vide"),
      Assert\Positive(message:"Le prix total doit être positif"),
      Assert\GreaterThan(value:0,message:"Le prix total doit être supérieur à 0"),
    ]
    #[Groups(['user','command'])]
    private $totalPrice = 0;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Le numéro de commande ne doit pas être vide"),
      Assert\NotNull(message:"Le numéro de commande ne doit pas être nulle"),
      Assert\Length(min:5,max:100, minMessage:"Le numero de commande doit être plus grand",maxMessage:"Le numéro de commande doit être plus petit")
      ]
      #[Groups(['user','command','product'])] 
    private $numCommand;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message:"La date de création ne peut être nulle"),
      Assert\NotBlank(message:"La date de création ne peut être vide"),
      Assert\Type(type: 'DateTime',message:"La date de création doit être de type date"),
      Assert\GreaterThanOrEqual(value: '-10 years',message:"La date de création est trop petite"),
      Assert\LessThanOrEqual(value: 'today',message:"La date de création est est trop grande")
    ]
    #[Groups(['user','command','product'])]
    private $createdAt;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull(message:"Le statut ne peut être nulle"),
      Assert\NotBlank(message:"Le statut ne peut être vide"),
      Assert\Positive(message:"Le statut doit être positif"),
      Assert\GreaterThanOrEqual(value: 1,message:"Le statut est trop petit"),
      Assert\LessThanOrEqual(value: 4,message:"Le statut est trop grand")
    ]
    #[Groups(['user','command','product'])]
    private $status;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'commands')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['command'])]
    private $products;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'commands')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['command'])]
    private $user = null;

    #[ORM\ManyToOne(targetEntity: Address::class, inversedBy: 'commands')]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['command'])]
    private $address;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotalPrice(): ?int
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(int $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getNumCommand(): ?string
    {
        return $this->numCommand;
    }

    public function setNumCommand(string $numCommand): self
    {
        $this->numCommand = $numCommand;

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

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        $this->products->removeElement($product);

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }
}
