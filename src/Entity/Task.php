<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use App\Repository\TaskRepository;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository", repositoryClass=TaskRepository::class)
 */
class Task
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Type("string")
     * @Serializer\SerializedName("title")
     */
    private $title;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Serializer\Type("float")
     * @Serializer\SerializedName("redmineEstimate")
     */
    private $redmineEstimate;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $redmineTotalSpend;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $evoTotalSpend;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $redmineId;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $taskId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Report::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $report;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $fromDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $toDate;

    /**
     * @ORM\OneToMany(targetEntity=Spending::class, mappedBy="task", orphanRemoval=true)
     */
    private $spendings;

    public function __construct()
    {
        $this->spendings = new ArrayCollection();
    }

    /**
     * @return Collection|Spending[]
     */
    public function getSpendings(): Collection
    {
        return $this->spendings;
    }

    public function removeSpendings(Spending $spending): self
    {
        if ($this->spendings->removeElement($spending)) {
            // set the owning side to null (unless already changed)
            if ($spending->getTask() === $this) {
                $spending->setTask(null);
            }
        }

        return $this;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getRedmineEstimate(): ?float
    {
        return $this->redmineEstimate;
    }

    public function setRedmineEstimate(?float $redmineEstimate): self
    {
        $this->redmineEstimate = $redmineEstimate;

        return $this;
    }

    public function getRedmineTotalSpend(): ?float
    {
        return $this->redmineTotalSpend;
    }

    public function setRedmineTotalSpend(?float $redmineTotalSpend): self
    {
        $this->redmineTotalSpend = $redmineTotalSpend;

        return $this;
    }

    public function getEvoTotalSpend(): ?float
    {
        return $this->evoTotalSpend;
    }

    public function setEvoTotalSpend(?float $evoTotalSpend): self
    {
        $this->evoTotalSpend = $evoTotalSpend;

        return $this;
    }

    public function getRedmineId(): ?int
    {
        return $this->redmineId;
    }

    public function setRedmineId(?int $redmineId): self
    {
        $this->redmineId = $redmineId;

        return $this;
    }

    public function getTaskId(): ?int
    {
        return $this->taskId;
    }

    public function setTaskId(?int $taskId): self
    {
        $this->taskId = $taskId;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(?Report $report): self
    {
        $this->report = $report;

        return $this;
    }

    public function getEmployerId(): string
    {
        return $this->employerId;
    }

    public function setEmployerId(?string $employerId): self
    {
        $this->employerId = $employerId;

        return $this;
    }

    public function getFromDate(): ?\DateTimeInterface
    {
        return $this->fromDate;
    }

    public function setFromDate(?\DateTimeInterface $fromDate): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getToDate(): ?\DateTimeInterface
    {
        return $this->toDate;
    }

    public function setToDate(?\DateTimeInterface $toDate): self
    {
        $this->toDate = $toDate;

        return $this;
    }
}
