<?php

namespace App\Model\Response;

use App\Model\EvoDTO\ProjectDTO;

class ProjectResponse extends SuccessResponse
{
    /**
     * @var ProjectDTO[]
     */
    public ?array $projects;
}
