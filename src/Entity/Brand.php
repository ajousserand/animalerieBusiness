<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Elasticsearch\DataProvider\Filter\OrderFilter;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
#[ApiResource(
    collectionOperations:['get','post'],
    itemOperations:['get'],
    normalizationContext:['groups'=>['brand']]
)]
#[ApiFilter(OrderFilter::class, properties: [ 'streetNumber' => 'desc'])]
class Brand
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['brand'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Le label ne doit pas être vide"),
      Assert\NotNull(message:"Le label ne doit pas être nulle"),
      Assert\Type(type:"string", message:"Le label doit être une chaine de caractère"),
      Assert\Length(min:5,max:50,minMessage:"Le label est trop petit",maxMessage:"Le label est trop grand")]
      #[Groups(['brand'])]
    private $label;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Le chemin de l'image ne doit pas être vide"),
      Assert\NotNull(message:"Le chemin de l'image ne doit pas être nulle"),
      Assert\Type(type:"string", message:"Le chemin de l'image doit être une chaine de caractère")]
      #[Groups(['brand'])]
    private $imagePath;

    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: Product::class)]
    #[Groups(['brand'])]
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(string $imagePath): self
    {
        $this->imagePath = $imagePath;

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
            $product->setBrand($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getBrand() === $this) {
                $product->setBrand(null);
            }
        }

        return $this;
    }
}
