<?php

namespace App\Entity;

use App\Repository\HardwareRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HardwareRepository::class)]
class Hardware
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 18)]
    private ?string $ipV4 = null;

    #[ORM\OneToMany(mappedBy: 'hardware', targetEntity: Booking::class, orphanRemoval: true)]
    private Collection $bookings;

    #[ORM\ManyToMany(targetEntity: Usergroup::class, mappedBy: 'hardware')]
    private Collection $usergroups;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->usergroups = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getIpV4(): ?string
    {
        return $this->ipV4;
    }

    public function setIpV4(string $ipV4): static
    {
        $this->ipV4 = $ipV4;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setHardware($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getHardware() === $this) {
                $booking->setHardware(null);
            }
        }
        
        return $this;
    }

    /**
     * @return Collection<int, Usergroup>
     */
    public function getUsergroups(): Collection
    {
        return $this->usergroups;
    }

    public function addUsergroup(Usergroup $usergroup): static
    {
        if (!$this->usergroups->contains($usergroup)) {
            $this->usergroups->add($usergroup);
            $usergroup->addHardware($this);
        }

        return $this;
    }

    public function removeUsergroup(Usergroup $usergroup): static
    {
        if ($this->usergroups->removeElement($usergroup)) {
            $usergroup->removeHardware($this);
        }

        return $this;
    }
}