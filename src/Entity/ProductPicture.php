<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProductPictureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductPictureRepository::class)]
#[ApiResource(
    collectionOperations:['get','post'],
    itemOperations:['get'],
    normalizationContext:['groups'=>['productPicture']]
)]
class ProductPicture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['productPicture','product'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Le chemin ne doit pas être vide"),
      Assert\NotNull(message:"Le champ ne doit pas être nulle"),
      Assert\Type(type:"string", message:"Le chemin doit être une chaine de caractère"),]
      #[Groups(['productPicture','product'])]
    private $path;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message:"Le libelle ne doit pas être nulle"),
      Assert\NotNull(message:"Le libelle ne doit pas être vide"),
      Assert\Type(type:"string", message:"Le libelle doit être une chaine de caractère"),
      Assert\Length(min:5,max:50,minMessage:"Le libelle est trop petit",maxMessage:"Le libelle est trop grand")]
    #[Groups(['productPicture'])]
    private $libelle;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'productPictures')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['productPicture'])]
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }
}
