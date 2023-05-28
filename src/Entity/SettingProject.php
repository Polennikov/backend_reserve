<?php

namespace App\Entity;

use App\Repository\SettingProjectRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SettingProjectRepository::class)
 */
class SettingProject
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
    private $lidId;

    /**
     * @ORM\Column(type="integer")
     */
    private $projectId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLidId(): ?int
    {
        return $this->lidId;
    }

    public function setLidId(?int $lidId): self
    {
        $this->lidId = $lidId;

        return $this;
    }

    public function getProjectId(): ?int
    {
        return $this->projectId;
    }

    public function setProjectId(int $projectId): self
    {
        $this->projectId = $projectId;

        return $this;
    }
}
