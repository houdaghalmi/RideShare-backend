<?php

namespace App\Entity;

use App\Enum\Gender;
use App\Enum\Occupation;
use App\Enum\UserRole;
use App\Enum\UserStatus;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'This email is already registered.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    private ?string $email = null;

    #[ORM\Column(length: 10, enumType: Gender::class)]
    #[Assert\NotNull]
    private ?Gender $gender = null;

    /** Hashed password (never the plain one). Validate the plain password in your form/DTO. */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 10, enumType: Occupation::class)]
    #[Assert\NotNull]
    private ?Occupation $occupation = null;

    #[ORM\Column(length: 10, enumType: UserStatus::class, options: ['default' => 'active'])]
    private UserStatus $status = UserStatus::Active;

    /** Required when occupation is STUDENT (see validateStudentInformation()). */
    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(max: 50)]
    private ?string $studentCard = null;

    /** Field of study ("filière"). Required when occupation is STUDENT. */
    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(max: 100)]
    private ?string $fieldOfStudy = null;

    /**
     * The user's single role. Chosen at registration among passenger / driver / both
     * (see UserRole::selectable()); ROLE_ADMIN is written directly in the database.
     */
    #[ORM\Column(length: 20, enumType: UserRole::class)]
    private UserRole $role = UserRole::Passenger;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $updatedAt;

    /** User 1 — 0..1 Vehicle (inverse side, FK lives on Vehicle). */
    #[ORM\OneToOne(mappedBy: 'owner', cascade: ['persist', 'remove'])]
    private ?Vehicle $vehicle = null;

    /** @var Collection<int, Ride> */
    #[ORM\OneToMany(targetEntity: Ride::class, mappedBy: 'driver')]
    private Collection $ridesDriven;

    /** @var Collection<int, Booking> */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'passenger')]
    private Collection $bookings;

    /** @var Collection<int, Message> */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender', fetch: 'EXTRA_LAZY')]
    private Collection $sentMessages;

    /** @var Collection<int, Notification> */
    #[ORM\OneToMany(targetEntity: Notification::class, mappedBy: 'recipient', fetch: 'EXTRA_LAZY')]
    private Collection $notifications;

    /** @var Collection<int, Review> */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'author')]
    private Collection $reviewsWritten;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
        $this->ridesDriven = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->sentMessages = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->reviewsWritten = new ArrayCollection();
    }

    #[ORM\PreUpdate]
    public function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[Assert\Callback]
    public function validateStudentInformation(ExecutionContextInterface $context): void
    {
        if (!$this->isStudent()) {
            return;
        }

        foreach (['studentCard' => $this->studentCard, 'fieldOfStudy' => $this->fieldOfStudy] as $path => $value) {
            if (null === $value) {
                $context->buildViolation('This field is required for students.')
                    ->atPath($path)
                    ->addViolation();
            }
        }
    }

    public function isStudent(): bool
    {
        return Occupation::Student === $this->occupation;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = mb_strtolower(trim($email));

        return $this;
    }

    public function getGender(): ?Gender
    {
        return $this->gender;
    }

    public function setGender(Gender $gender): static
    {
        $this->gender = $gender;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getFieldOfStudy(): ?string
    {
        return $this->fieldOfStudy;
    }

    public function setFieldOfStudy(?string $fieldOfStudy): static
    {
        $this->fieldOfStudy = self::normalize($fieldOfStudy);

        return $this;
    }

    public function getStudentCard(): ?string
    {
        return $this->studentCard;
    }

    public function setStudentCard(?string $studentCard): static
    {
        $this->studentCard = self::normalize($studentCard);

        return $this;
    }

    public function getOccupation(): ?Occupation
    {
        return $this->occupation;
    }

    public function setOccupation(Occupation $occupation): static
    {
        $this->occupation = $occupation;

        return $this;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function setStatus(UserStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    /** @return list<string> */
    public function getRoles(): array
    {
        return [$this->role->value];
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

    public function setRole(UserRole $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated since Symfony 7.3, kept for interface compatibility
    }

    /** Avoid storing the real password hash in the session. */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', (string) $this->password);

        return $data;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): static
    {
        $this->vehicle = $vehicle;

        if (null !== $vehicle && $vehicle->getOwner() !== $this) {
            $vehicle->setOwner($this);
        }

        return $this;
    }

    public function hasVehicle(): bool
    {
        return null !== $this->vehicle;
    }

    /** @return Collection<int, Ride> */
    public function getRidesDriven(): Collection
    {
        return $this->ridesDriven;
    }

    public function addRideDriven(Ride $ride): static
    {
        if (!$this->ridesDriven->contains($ride)) {
            $this->ridesDriven->add($ride);
            $ride->setDriver($this);
        }

        return $this;
    }

    /** @return Collection<int, Booking> */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setPassenger($this);
        }

        return $this;
    }

    /** @return Collection<int, Message> */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function addSentMessage(Message $message): static
    {
        if (!$this->sentMessages->contains($message)) {
            $this->sentMessages->add($message);
            $message->setSender($this);
        }

        return $this;
    }

    /** @return Collection<int, Notification> */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setRecipient($this);
        }

        return $this;
    }

    /** @return Collection<int, Review> */
    public function getReviewsWritten(): Collection
    {
        return $this->reviewsWritten;
    }

    public function addReviewWritten(Review $review): static
    {
        if (!$this->reviewsWritten->contains($review)) {
            $this->reviewsWritten->add($review);
            $review->setAuthor($this);
        }

        return $this;
    }

    /** Trims the value and turns blank strings into null so "required" checks cannot be bypassed with spaces. */
    private static function normalize(?string $value): ?string
    {
        $value = null === $value ? null : trim($value);

        return '' === $value ? null : $value;
    }
}