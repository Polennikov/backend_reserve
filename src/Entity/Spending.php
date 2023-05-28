<?php

namespace App\Entity;

use App\Repository\SpendingRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=SpendingRepository::class)
 */
class Spending
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("id")
     * @Serializer\Groups({"report"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("month")
     * @Serializer\Groups({"report"})
     */
    private $month;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Type("float")
     * @Serializer\SerializedName("timeEvo")
     * @Serializer\Groups({"report"})
     */
    private $timeEvo;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Type("float")
     * @Serializer\SerializedName("timeRedmine")
     * @Serializer\Groups({"report"})
     */
    private $timeRedmine;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("year")
     * @Serializer\Groups({"report"})
     */
    private $year;

    /**
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("employerId")
     * @Serializer\Groups({"report"})
     */
    private $employerId;

    /**
     * @ORM\ManyToOne(targetEntity=Task::class, inversedBy="spendings")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Serializer\Type("App\Entity\Task")
     * @Serializer\SerializedName("report")
     */
    private $task;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Serializer\Type("DateTimeInterface<'Y-m-d'>")
     * @Serializer\SerializedName("fromDate")
     */
    //private $dateEvo;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Serializer\Type("DateTimeInterface<'Y-m-d'>")
     * @Serializer\SerializedName("toDate")
     */
    //private $dateRedmine;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function setMonth(int $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getTimeEvo(): ?float
    {
        return $this->timeEvo;
    }

    public function setTimeEvo(?float $timeEvo): self
    {
        $this->timeEvo = $timeEvo;

        return $this;
    }

    public function getTimeRedmine(): ?float
    {
        return $this->timeRedmine;
    }

    public function setTimeRedmine(?float $timeRedmine): self
    {
        $this->timeRedmine = $timeRedmine;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getEmployerId(): ?string
    {
        return $this->employerId;
    }

    public function setEmployerId(?string $employerId): self
    {
        $this->employerId = $employerId;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }

    public function getDateEvo(): ?\DateTimeInterface
    {
        return $this->dateEvo;
    }

    public function setDateEvo(?\DateTimeInterface $dateEvo): self
    {
        $this->dateEvo = $dateEvo;

        return $this;
    }

    public function getDateRedmine(): ?\DateTimeInterface
    {
        return $this->dateRedmine;
    }

    public function setDateRedmine(?\DateTimeInterface $dateRedmine): self
    {
        $this->dateRedmine = $dateRedmine;

        return $this;
    }
}
