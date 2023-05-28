<?php

namespace App\Entity;

use App\Repository\SettingManagerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SettingManagerRepository::class)
 */
class SettingManager
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $countMonth;

    /**
     * @ORM\OneToOne(targetEntity=Manager::class)
     */
    private $manager;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $projectsSidebar;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $fullName;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountMonth(): ?int
    {
        return $this->countMonth;
    }

    public function setCountMonth(?int $countMonth): self
    {
        $this->countMonth = $countMonth;

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

    public function getProjectsSidebar(): ?string
    {
        return $this->projectsSidebar;
    }

    public function setProjectsSidebar(?string $projectsSidebar): self
    {
        $this->projectsSidebar = $projectsSidebar;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }
}
