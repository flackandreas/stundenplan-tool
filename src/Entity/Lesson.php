<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Lesson
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'lesson', targetEntity: ScheduleEntry::class, orphanRemoval: true)]
    private Collection $scheduleEntries;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    #[ORM\Column]
    private ?int $hoursPerWeek = null;

    #[ORM\Column]
    private bool $isDoublePeriod = false;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?School $school = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Teacher $teacher = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?StudentClass $studentClass = null;

    #[ORM\ManyToOne]
    private ?Room $room = null;

    public function __construct()
    {
        $this->scheduleEntries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, ScheduleEntry>
     */
    public function getScheduleEntries(): Collection
    {
        return $this->scheduleEntries;
    }

    public function addScheduleEntry(ScheduleEntry $scheduleEntry): static
    {
        if (!$this->scheduleEntries->contains($scheduleEntry)) {
            $this->scheduleEntries->add($scheduleEntry);
            $scheduleEntry->setLesson($this);
        }

        return $this;
    }

    public function removeScheduleEntry(ScheduleEntry $scheduleEntry): static
    {
        if ($this->scheduleEntries->removeElement($scheduleEntry)) {
            // set the owning side to null (unless already changed)
            if ($scheduleEntry->getLesson() === $this) {
                $scheduleEntry->setLesson(null);
            }
        }

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;
        return $this;
    }

    public function getHoursPerWeek(): ?int
    {
        return $this->hoursPerWeek;
    }

    public function setHoursPerWeek(int $hoursPerWeek): static
    {
        $this->hoursPerWeek = $hoursPerWeek;

        return $this;
    }

    public function isDoublePeriod(): ?bool
    {
        return $this->isDoublePeriod;
    }

    public function setDoublePeriod(bool $isDoublePeriod): static
    {
        $this->isDoublePeriod = $isDoublePeriod;

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

    public function getTeacher(): ?Teacher
    {
        return $this->teacher;
    }

    public function setTeacher(?Teacher $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getStudentClass(): ?StudentClass
    {
        return $this->studentClass;
    }

    public function setStudentClass(?StudentClass $studentClass): static
    {
        $this->studentClass = $studentClass;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }
}
