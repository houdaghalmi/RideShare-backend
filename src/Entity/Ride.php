<?php

namespace App\Entity;

use App\Enum\GenderPreference;
use App\Enum\RideStatus;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Index(columns: ['status', 'departure_at'])]
class Ride
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    public string $departure = '';

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 150)]
    #[Assert\NotEqualTo(propertyPath: 'departure')]
    public string $destination = '';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $departureAt;

    #[ORM\Column(length: 10, enumType: GenderPreference::class)]
    public GenderPreference $gender = GenderPreference::Any;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    #[Assert\Range(min: 0, max: 8)]
    public int $availableSeats = 1;

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2)]
    #[Assert\PositiveOrZero]
    public string $price = '0.00';

    #[ORM\Column(type: Types::DECIMAL, precision: 8, scale: 2, nullable: true)]
    #[Assert\PositiveOrZero]
    public ?string $suggestedPrice = null;

    #[ORM\Column(length: 10, enumType: RideStatus::class)]
    public RideStatus $status = RideStatus::Open;

    #[ORM\Column(options: ['default' => false])]
    public bool $smokingAllowed = false;

    #[ORM\Column(options: ['default' => true])]
    public bool $luggageAllowed = true;

    #[ORM\Column(options: ['default' => true])]
    public bool $musicAllowed = true;

    #[ORM\Column(options: ['default' => false])]
    public bool $instantBooking = false;

    public function __construct(
        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        public User $driver,
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}