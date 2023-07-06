<?php

namespace App\Entity;

use App\Repository\UserGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserGroupRepository::class)]
class UserGroup
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'userGroup', targetEntity: User::class)]
    private Collection $userList;

    #[ORM\ManyToMany(targetEntity: Hardware::class, inversedBy: 'usergroups')]
    private Collection $hardware;

    public function __construct()
    {
        $this->userList = new ArrayCollection();
        $this->hardware = new ArrayCollection();
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

    /**
     * @return Collection<int, User>
     */
    public function getUserList(): Collection
    {
        return $this->userList;
    }

    public function addUserList(User $userList): static
    {
        if (!$this->userList->contains($userList)) {
            $this->userList->add($userList);
            $userList->setUserGroup($this);
        }

        return $this;
    }

    public function removeUserList(User $userList): static
    {
        if ($this->userList->removeElement($userList)) {
            // set the owning side to null (unless already changed)
            if ($userList->getUserGroup() === $this) {
                $userList->setUserGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Hardware>
     */
    public function getHardware(): Collection
    {
        return $this->hardware;
    }

    public function addHardware(Hardware $hardware): static
    {
        if (!$this->hardware->contains($hardware)) {
            $this->hardware->add($hardware);
        }

        return $this;
    }

    public function removeHardware(Hardware $hardware): static
    {
        $this->hardware->removeElement($hardware);

        return $this;
    }
}
