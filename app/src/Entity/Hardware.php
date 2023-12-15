<?php

namespace App\Entity;

use App\Repository\HardwareRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HardwareRepository::class)]
class Hardware implements \Stringable {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 18)]
    #[Assert\Ip]
    private ?string $ipV4 = null;

    #[ORM\OneToMany(mappedBy: 'hardware', targetEntity: Booking::class, orphanRemoval: true)]
    private Collection $bookings;

    #[ORM\ManyToMany(targetEntity: Role::class, mappedBy: 'hardwares')]
    private Collection $roles;

    #[ORM\Column(length: 255)]
    private ?string $vpnUserName = null;

    public function __construct() {
        $this->bookings = new ArrayCollection();
        $this->roles = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(?string $description): static {
        $this->description = $description;

        return $this;
    }

    public function getIpV4(): ?string {
        return $this->ipV4;
    }

    public function setIpV4(string $ipV4): static {
        $this->ipV4 = $ipV4;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static {
        if(!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setHardware($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static {
        if($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if($booking->getHardware() === $this) {
                $booking->setHardware(null);
            }
        }

        return $this;
    }

    public function __toString(): string {
        return $this->name;
    }

    /**
     * @return Collection<int, Role>
     */
    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
            $role->addHardware($this);
        }

        return $this;
    }

    public function removeRole(Role $role): static
    {
        if ($this->roles->removeElement($role)) {
            $role->removeHardware($this);
        }

        return $this;
    }

    public function getVpnUserName(): ?string
    {
        return $this->vpnUserName;
    }

    public function setVpnUserName(string $vpnUserName): static
    {
        $this->vpnUserName = $vpnUserName;

        return $this;
    }
}