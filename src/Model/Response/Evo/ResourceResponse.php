<?php

namespace App\Model\Response\Evo;

use App\Model\EvoDTO\FactDTO;
use App\Model\EvoDTO\PlanResourceDTO;
use App\Model\Response\SuccessResponse;

class ResourceResponse extends SuccessResponse
{
    /**
     * @var FactDTO[]
     */
    public ?array $freeTime;

    /**
     * @var PlanResourceDTO[]
     */
    public ?array $planPercent;

    public function __construct(bool $success, array $freeTime, array $planPercent)
    {
        parent::__construct($success);
        $this->freeTime = $freeTime;
        $this->planPercent = $planPercent;
    }
}
