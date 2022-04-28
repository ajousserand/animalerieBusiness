<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ReviewRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ApiResource(
    collectionOperations:['get','post'],
    itemOperations:['get'],
    normalizationContext:['groups'=>['review']]
)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['user', 'review', 'product'])]
    private $id;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull(message:"La notene peut être nulle"),
      Assert\NotBlank(message:"La note ne peut être vide"),
      Assert\Positive(message:"La note chiffre doit être positif"),
      Assert\GreaterThanOrEqual(value: 0,message:"La note est trop petite"),
      Assert\LessThanOrEqual(value: 20,message:"La note est trop grande")
    ]
    #[Groups(['user','review','product'])]
    private $note;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull(message:"La date de création ne peut être nulle"),
      Assert\NotBlank(message:"La date de création ne peut être vide"),
      Assert\Type(type: 'DateTime',message:"La date de creéation doit comporter une date"),
      Assert\GreaterThanOrEqual(value: '-10 years',message:"La date de création est trop petite"),
      Assert\LessThanOrEqual(value: 'today',message:"La date de création est est trop grande")
    ]
    #[Groups(['user','review','product'])]
    private $createdAt;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message:"Le contenu ne doit pas être nulle"),
      Assert\NotNull(message:"Le contenu ne doit pas être vide"),
      Assert\Length(min:5,max:5000,minMessage:"Le contenu est trop petit",maxMessage:"Le contenu est trop grand"),
      Assert\Type(type:"string", message:"Le contenudoit être une chaine de caractère"),]
    #[Groups(['user','review','product'])]
    private $content;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['user','review'])]
    private $product;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reviews')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['review'])]
    private $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
