<?php

namespace App\Entity;

use App\Repository\CitationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CitationRepository::class)]
class Citation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: "Le nom de l'auteur est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "Le nom de l'auteur doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le nom de l'auteur ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $auteur = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le texte de la citation est obligatoire.')]
    #[Assert\Length(
        min: 5,
        max: 5000,
        minMessage: 'La citation doit contenir au moins {{ limit }} caractères.',
        maxMessage: 'La citation ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $text = null;

    #[ORM\Column]
    #[Assert\NotNull(message: "La date d'ajout est obligatoire.")]
    private ?\DateTime $datetime = null;

    #[ORM\Column(length: 15, nullable: true)]
    #[Assert\Length(
        max: 15,
        maxMessage: "L'année de publication ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: '/^-?\d{1,4}$/',
        message: "L'année de publication doit être un nombre (par exemple 1943 ou -350)."
    )]
    private ?string $publishing_year = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        max: 255,
        maxMessage: 'La source ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $source = null;

    #[ORM\Column(length: 30, nullable: true)]
    #[Assert\Length(
        max: 30,
        maxMessage: 'La catégorie ne peut pas dépasser {{ limit }} caractères.'
    )]
    private ?string $category = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(
        max: 100,
        maxMessage: "L'univers ne peut pas dépasser {{ limit }} caractères."
    )]
    private ?string $universe = null;

    #[ORM\Column]
    #[Assert\Count(
        max: 10,
        maxMessage: 'Vous ne pouvez pas associer plus de {{ limit }} tags à une citation.'
    )]
    #[Assert\All([
        new Assert\NotBlank(message: 'Un tag ne peut pas être vide.'),
        new Assert\Length(
            max: 50,
            maxMessage: 'Un tag ne peut pas dépasser {{ limit }} caractères.'
        ),
    ])]
    private array $tags = [];

    public function __construct()
    {
        $this->datetime = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuteur(): ?string
    {
        return $this->auteur;
    }

    public function setAuteur(string $auteur): static
    {
        $this->auteur = $auteur;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getDatetime(): ?\DateTime
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTime $datetime): static
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getPublishingYear(): ?string
    {
        return $this->publishing_year;
    }

    public function setPublishingYear(?string $publishing_year): static
    {
        $this->publishing_year = $publishing_year;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getUniverse(): ?string
    {
        return $this->universe;
    }

    public function setUniverse(?string $universe): static
    {
        $this->universe = $universe;

        return $this;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setTags(array $tags): static
    {
        $this->tags = $tags;

        return $this;
    }
}
