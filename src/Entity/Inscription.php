<?php

namespace App\Entity;

use App\Repository\InscriptionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InscriptionRepository::class)]
class Inscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity:Employe::class)]
    #[ORM\JoinColumn(nullable:true)]
    private $lemploye = null;

    #[ORM\ManyToOne(targetEntity:Formation::class)]
    #[ORM\JoinColumn(nullable:true)]
    private $laformation = null;

    #[ORM\Column]
    private ?int $statut = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function setStatut(int $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getLemploye(): ?Employe
    {
        return $this->lemploye;
    }

    public function setLemploye(?Employe $lemploye): static
    {
        $this->lemploye = $lemploye;

        return $this;
    }

    public function getLaformation(): ?Formation
    {
        return $this->laformation;
    }

    public function setLaformation(?Formation $laformation): static
    {
        $this->laformation = $laformation;

        return $this;
    }
}
