<?php

namespace App\Entity;

use App\Repository\RoleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
class Role implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Hardware::class, inversedBy: 'roles', fetch: "EXTRA_LAZY")]
    #[ORM\JoinTable(name: 'role_hardware')]
    private Collection $hardwares;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'userRoles', fetch: "EXTRA_LAZY")]
    #[ORM\JoinTable(name: 'role_user')]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->hardwares = new ArrayCollection();
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

    /**
     * @return Collection<int, Hardware>
     */
    public function getHardwares(): Collection
    {
        return $this->hardwares;
    }

    public function addHardware(Hardware $hardware): static
    {
        if (!$this->hardwares->contains($hardware)) {
            $this->hardwares->add($hardware);
        }

        return $this;
    }

    public function removeHardware(Hardware $hardware): static
    {
        $this->hardwares->removeElement($hardware);

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }
}
