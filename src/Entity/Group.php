<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;

class Group
{
    private ?string $id;

    private string $name;

    private User $owner;

    private \DateTime $createdAt;

    private \DateTime $updatedAt;

    /** @var Collection|User[] */
    private Collection $users;

    /** @var Collection|Category[] */
    private Collection $categories;

    /**
     * @throws \Exception
     */
    public function __construct(string $name, User $owner, string $id = null)
    {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->name = $name;
        $this->owner = $owner;
        $this->createdAt = new \DateTime();
        $this->users = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->markAsUpdated();
    }

    /**
     * Get the value of id.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the value of name.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name.
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of owner.
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Get the value of createdAt.
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get the value of updatedAt.
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function markAsUpdated(): void
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): void
    {
        $this->users->add($user);

        $user->addGroup($this);
    }

    public function removeUser(User $user): void
    {
        $this->users->removeElement($user);

        $user->removeGroup($this);
    }

    public function isOwnerBy(User $user)
    {
        return $this->getOwner()->getId() === $user->getId();
    }

    /**
     * @return Collection|Category[]
     */ 
    public function getCategories()
    {
        return $this->categories;
    }
}
