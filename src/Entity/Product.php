<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Form\Forms;
use App\Repository\ProductRepository;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    // Annotation de contraintes du validator
    #[Assert\NotBlank(message: 'Le nom du produit est obligatoire')]
    #[Assert\Length(min: 3, max: 255,  minMessage: '3 CARAC mini')]
    // #[Assert\Length(min: 10,  minMessage: '10 CARAC mini', groups: ["large-name"])]
    private $name;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(message: 'Le prix du produit est obligatoire')]
    private $price;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'products')]
    private $category;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: 'La photo doit être obligatoire')]
    #[Assert\Url(message: 'La photo principale doit être une url valide')]
    private $picture;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'La description courte doit être obligatoire')]
    #[Assert\Length(min: 20,  minMessage: 'La description courte doit faire minimum 20 caractères')]
    private $shortDescription;


    // // Ajout de fonction validator dans objet
    // public static function loadValidatorMetadata(ClassMetadata $metadata) {
    //     $validator = Validation::createValidator();

    //     $formFactory = Forms::createFormFactoryBuilder()
    //     ->addExtension(new ValidatorExtension($validator))
    //     ->getFormFactory();
    //     $metadata->addPropertyConstraints('name', [
    //         new Assert\NotBlank(['message' => 'Le nom du produit est obligatoire']),
    //         new Assert\Length(['min' => 3, 'max' => 255, 'minMessage' => 'Le nom du produit doit contenir au moins 3 caractères' ])
    //     ]);
    //     $metadata->addPropertyConstraint('price', new Assert\NotBlank(['message' => 'Le prix du produit est obligatoire']));
    // }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    // Le "?" permet de pouvoir envoyer un string null sans retour erreur (avant le sql)
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getUppercaseName() : string {
        return strtoupper($this->name);
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(?string $shortDescription): self
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

}
