<?php

namespace App\Entity;

use App\Repository\ReportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass=ReportRepository::class)
 */
class Report
{
    public const KEY_REDMINE = 'redmine';

    public const KEY_EVO = 'evo';

    public const DISPLAY_TOTAL_TIME_CODE = 'displayTotalTime';
    public const DISPLAY_TOTAL_TIME_FOR_TASK_WITHOUT_REDMINE_CODE = 'displayTotalTimeForTaskWithoutRedmine';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Type("integer")
     * @Serializer\SerializedName("id")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Type("DateTimeInterface<'Y-m-d H:i:s'>")
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Manager::class, inversedBy="reports")
     * @ORM\JoinColumn(name="manager_id", referencedColumnName="id")
     * @Serializer\Type("App\Entity\Manager")
     */
    private $manager;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Serializer\SerializedName("projectRm")
     */
    private $projectRm;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Serializer\SerializedName("projectEvo")
     */
    private $projectEvo;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="report", orphanRemoval=true)
     * @Serializer\Type("ArrayCollection<App\Entity\Task>")
     * @Serializer\SerializedName("tasks")
     */
    private $tasks;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Serializer\Type("DateTimeInterface<'Y-m-d'>")
     * @Serializer\SerializedName("fromDate")
     */
    private $fromDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Serializer\Type("DateTimeInterface<'Y-m-d'>")
     * @Serializer\SerializedName("toDate")
     */
    private $toDate;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Serializer\Type("array|null")
     * @Serializer\SerializedName("filterSettings")
     */
    private $filterSettings = [];

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getDateIntervalByMonth($translator = null): array
    {
        if (!$this->getFromDate() && !$this->getToDate()) {
            return [];
        }
        $datesForColumns = [];
        $fromDate = new \DateTime($this->getFromDate()->format('Y-m'));
        $toDate = new \DateTime($this->getToDate()->format('Y-m'));

        $interval = $toDate->diff($fromDate);
        $months = $interval->y * 12 + $interval->m;
        if (0 >= $months) {
            $period = [$fromDate];
        } else {
            $period = new \DatePeriod($fromDate, new \DateInterval('P1M'), $months);
        }

        foreach ($period as $date) {
            if ($translator) {
                $month = $translator->trans('month.' . $date->format('F'));
            } else {
                $month = $date->format('F');
            }
            $datesForColumns[$date->format('Y')][$date->format('m')] = $month;
        }

        return $datesForColumns;
    }


    public function getStringDateFromTo(string $format = 'd.m.Y', string $separator = ' - '): string
    {
        $fromDate = $this->getFromDate() ? $this->getFromDate()->format($format) : '';
        $toDate = $this->getToDate() ? $this->getToDate()->format($format) : '';
        return $fromDate . $separator . $toDate;
    }

    public function getStringCreatedAt(string $format = 'd.m.Y'): string
    {
        return $this->getCreatedAt() ? $this->getCreatedAt()->format($format) : '';
    }

    public function getTotalSpendByDate(int $monthNumber, int $year, string $service): float
    {
        $totalSpend = 0;
        foreach($this->getTasks() as $task) {
            $totalSpend += $task->getTotalSpendByDate($monthNumber, $year, $service);
        }

        return $totalSpend;
    }

    public function getTotalSpend(string $service): float
    {
        $totalSpend = 0;
        foreach($this->getTasks() as $task) {
            if (null !== $task->getRedmineId() || $this->isDisplayTotalTimeForTaskWithoutRedmine()) {
                if (self::KEY_REDMINE === $service) {
                    $totalSpend += $task->getRedmineTotalSpend();
                } else if (self::KEY_EVO === $service) {
                    $totalSpend += $task->getEvoTotalSpend();
                }
            }
        }

        return $totalSpend;
    }

    public function getTotalEstimate(): float
    {
        $totalEstimate = 0;
        foreach($this->getTasks() as $task) {
            $totalEstimate += $task->getRedmineEstimate();
        }

        return $totalEstimate;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getManager(): ?Manager
    {
        return $this->manager;
    }

    public function setManager(?Manager $manager): self
    {
        $this->manager = $manager;

        return $this;
    }

    public function getProjectRm(): ?string
    {
        return $this->projectRm;
    }

    public function setProjectRm(string $project): self
    {
        $this->projectRm = $project;

        return $this;
    }

    public function getProjectEvo(): ?string
    {
        return $this->projectEvo;
    }

    public function setProjectEvo(string $project): self
    {
        $this->projectEvo = $project;

        return $this;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setReport($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getReport() === $this) {
                $task->setReport(null);
            }
        }

        return $this;
    }

    public function getStatuses(): ?string
    {
        return $this->statuses;
    }

    public function setStatuses(?string $statuses): self
    {
        $this->statuses = $statuses;

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

    public function getFilterSettings(): ?array
    {
        return $this->filterSettings;
    }

    public function setFilterSettings(?array $filterSettings): self
    {
        $this->filterSettings = $filterSettings;

        return $this;
    }

    public function isDisplayTotalTime(): ?bool
    {
        return $this->getFilterSetting(self::DISPLAY_TOTAL_TIME_CODE);
    }

    public function isDisplayTotalTimeForTaskWithoutRedmine(): ?bool
    {
        return $this->getFilterSetting(self::DISPLAY_TOTAL_TIME_FOR_TASK_WITHOUT_REDMINE_CODE);
    }

}
