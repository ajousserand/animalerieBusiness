<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CityRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ApiResource(
    collectionOperations:['get','post'],
    itemOperations:['get'],
    normalizationContext:['groups'=>['city']]
)]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'name' => 'partial', 'cp' => 'exact'])]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['city'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotNull(message:"Le nom de la ville ne peut être nulle"),
      Assert\NotBlank(message:"Le nom de la ville ne peut être vide"),
      Assert\Length(min:5,max:100, minMessage:"Le nom de la ville doit être plus long",maxMessage:"Le nom de la ville doit être plus court"),
      Assert\Type(type:"string", message:"Le nom de la ville doit être une chaine de caractère"),
    ]
    #[Groups(['city'])]
    private $name;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull(message:"Le code postal ne peut être nulle"),
      Assert\NotBlank(message:"Le code postal ne peut être vide"),
      Assert\Positive(message:"Le code postal doit être positif"),
      Assert\GreaterThanOrEqual(value: 1000,message:"Le code postal est trop petit"),
      Assert\LessThanOrEqual(value: 98000,message:"Le code postal est trop grand"),
      Assert\Type(type:"integer", message:"Le code postal doit être une chaine de caractère"),
    ]
    #[Groups(['city','address'])]
    private $cp;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: Address::class)]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['city'])]
    private $addresses;

    public function __construct()
    {
        $this->addresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCp(): ?int
    {
        return $this->cp;
    }

    public function setCp(int $cp): self
    {
        $this->cp = $cp;

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
            $address->setCity($this);
        }

        return $this;
    }

    public function removeAddress(Address $address): self
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getCity() === $this) {
                $address->setCity(null);
            }
        }

        return $this;
    }
}
