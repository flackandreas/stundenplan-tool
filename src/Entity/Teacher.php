<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\School;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
class Teacher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 2552)]
    private ?string $name = null;

    #[ORM\Column(length: 10)]
    private ?string $shortcode = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\ManyToOne(inversedBy: 'teachers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    #[ORM\OneToOne(mappedBy: 'teacher', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'teacher', targetEntity: TeacherAvailability::class, orphanRemoval: true)]
    private Collection $availabilities;

    public function __construct()
    {
        $this->availabilities = new ArrayCollection();
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

    public function getShortcode(): ?string
    {
        return $this->shortcode;
    }

    public function setShortcode(string $shortcode): static
    {
        $this->shortcode = $shortcode;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getSchool(): ?School
    {
        return $this->school;
    }

    public function setSchool(?School $school): static
    {
        $this->school = $school;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        // unset the owning side of the relation if necessary
        if ($user === null && $this->user !== null) {
            $this->user->setTeacher(null);
        }

        // set the owning side of the relation if necessary
        if ($user !== null && $user->getTeacher() !== $this) {
            $user->setTeacher($this);
        }

        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, TeacherAvailability>
     */
    public function getAvailabilities(): Collection
    {
        return $this->availabilities;
    }

    public function addAvailability(TeacherAvailability $availability): static
    {
        if (!$this->availabilities->contains($availability)) {
            $this->availabilities->add($availability);
            $availability->setTeacher($this);
        }

        return $this;
    }

    public function removeAvailability(TeacherAvailability $availability): static
    {
        if ($this->availabilities->removeElement($availability)) {
            // set the owning side to null (unless already changed)
            if ($availability->getTeacher() === $this) {
                $availability->setTeacher(null);
            }
        }

        return $this;
    }
}
