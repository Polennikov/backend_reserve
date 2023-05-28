<?php

namespace App\Model\Response\Rm;

use App\Model\Response\SuccessResponse;
use App\Model\RmDTO\TaskDTO;

class TaskResponse extends SuccessResponse
{
    /**
     * @var TaskDTO[]|null
     *
     */
    public ?array $tasks;

    public function __construct(bool $success, array $tasks)
    {
        $this->tasks = $tasks;
        parent::__construct($success);
    }
}
