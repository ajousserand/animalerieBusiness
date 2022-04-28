<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CountVisitController;
use App\Repository\VisiteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: VisiteRepository::class)]
#[ApiResource(
    collectionOperations:['get','post',
    'get_total_visits_from_dates' => [
        'method'=>'GET',
        'path'=> '/visit/get_total_visits',
        'controller'=> CountVisitController::class
    ]],
    itemOperations:['get'],
    normalizationContext:['groups'=>['visite']]
)]
class Visite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['visite'])]
    private $id;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message:"La date de visite ne doit pas être vide"),
      Assert\NotNull(message:"La date de visite ne doit pas être nulle"),
      Assert\Type(type:"dateTime", message:"La date de visite doit être une date"),
      Assert\EqualTo('today')]
      #[Groups(['visite'])]
    private $visitedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVisitedAt(): ?\DateTimeInterface
    {
        return $this->visitedAt;
    }

    public function setVisitedAt(\DateTimeInterface $visitedAt): self
    {
        $this->visitedAt = $visitedAt;

        return $this;
    }
}
