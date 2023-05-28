<?php

namespace App\Model\Response\Rm;

use App\Model\Response\SuccessResponse;
use App\Model\RmDTO\ProjectDTO;

class ProjectResponse extends SuccessResponse
{
    /**
     * @var ProjectDTO[]|null
     *
     */
    public ?array $projects;

    public function __construct(bool $success, array $projects)
    {
        $this->projects = $projects;
        parent::__construct($success);
    }
}
