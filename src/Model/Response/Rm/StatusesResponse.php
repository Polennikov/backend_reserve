<?php

namespace App\Model\Response\Rm;

use App\Model\Response\SuccessResponse;
use App\Model\RmDTO\StatusesDTO;

class StatusesResponse extends SuccessResponse
{
    /**
     * @var StatusesDTO[]
     *
     */
    public $statuses;

    public function __construct(bool $success, array $statuses)
    {
        parent::__construct($success);
        $arrayStatuses = [];
        foreach ($statuses as $statusesItem) {
            $statusesDTO = new StatusesDTO();
            $statusesDTO->id = $statusesItem['id'];
            $statusesDTO->name = $statusesItem['name'];
            $statusesDTO->isClosed = $statusesItem['is_closed'];
            $arrayStatuses[] = $statusesDTO;
        }
        $this->statuses = $arrayStatuses;
    }
}
