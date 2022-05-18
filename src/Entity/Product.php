<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\TotalProductSoldDescController;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiResource(
    collectionOperations:['get','post',
    'get_total_product_from_dates' => [
        'method'=>'GET',
        'path'=> '/product/get_total_product',
        'controller'=> TotalProductSoldDescController::class
    ]],
    itemOperations:['get'],
    normalizationContext:['groups'=>['product']]
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['review','productPicture','brand','command','category','product'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Le label ne doit pas être vide"),
    Assert\NotNull(message:"Le label ne doit pas être nulle"),
    Assert\Type(type:"string", message:"Le label doit être une chaine de caractère"),
    Assert\Length(min:5,max:50,minMessage:"Le label est trop petit",maxMessage:"Le label est trop grand")]
    #[Groups(['review','productPicture','brand','command','category','product','user'])]
    private $label;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message:"La description ne doit pas être vide"),
    Assert\NotNull(message:"La description ne doit pas être nulle"),
    Assert\Type(type:"string", message:"La description doit être une chaine de caractère"),
    Assert\Length(min:10,max:5000,minMessage:"La description est trop petit",maxMessage:"La description est trop grand")]
    #[Groups(['product'])]
    private $description;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message:"Le prix ne doit pas être vide"),
      Assert\NotNull(message:"Le prix ne doit pas être nulle"),
      Assert\Positive(message:"Le prix doit être positif"),
      Assert\Type(type:"integer", message:"Le prix doit être un chiffre")
    ]
    #[Groups(['review','command','product'])]
    private $price;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotBlank(message:"Le stock ne doit pas être vide"),
      Assert\NotNull(message:"Le stock ne doit pas être nulle"),
      Assert\Positive(message:"Le stock doit être positif"),
      Assert\Type(type:"integer", message:"Le stock doit être un chiffre")
    ]
    #[Groups(['product'])]
    private $stock;

    #[ORM\Column(type: 'boolean')]
    #[Assert\NotBlank(message:"La validité ne doit pas être vide"),
      Assert\NotNull(message:"La validité ne doit pas être nulle"),
      Assert\Type(type:'boolean',message:"La validité doit être booléen")]
      #[Groups(['product'])]
    private $isActive;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: ProductPicture::class)]
    #[Groups(['product'])]
    private $productPictures;

    #[ORM\ManyToMany(targetEntity: Category::class, mappedBy: 'products')]
    #[Groups(['product'])]
    private $categories;

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product'])]
    private $brand;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Review::class)]
    #[Groups(['product'])]
    private $reviews;

    #[ORM\ManyToMany(targetEntity: Command::class, mappedBy: 'products')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['product'])]
    private $commands;

    public function __construct()
    {
        $this->productPictures = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->commands = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(?int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection<int, ProductPicture>
     */
    public function getProductPictures(): Collection
    {
        return $this->productPictures;
    }

    public function addProductPicture(ProductPicture $productPicture): self
    {
        if (!$this->productPictures->contains($productPicture)) {
            $this->productPictures[] = $productPicture;
            $productPicture->setProduct($this);
        }

        return $this;
    }

    public function removeProductPicture(ProductPicture $productPicture): self
    {
        if ($this->productPictures->removeElement($productPicture)) {
            // set the owning side to null (unless already changed)
            if ($productPicture->getProduct() === $this) {
                $productPicture->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addProduct($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->removeElement($category)) {
            $category->removeProduct($this);
        }

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

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
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
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
            $command->addProduct($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->commands->removeElement($command)) {
            $command->removeProduct($this);
        }

        return $this;
    }
}
