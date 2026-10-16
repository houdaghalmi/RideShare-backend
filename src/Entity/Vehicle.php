<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public string $brand = '';

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    public string $model = '';

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 30)]
    public string $color = '';

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    #[Assert\Range(min: 1, max: 8)]
    public int $seats = 4;

    public function __construct(
        #[ORM\OneToOne(inversedBy: 'vehicle')]
        #[ORM\JoinColumn(nullable: false, unique: true, onDelete: 'CASCADE')]
        public User $owner,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}