<?php

namespace App\Model\Response\Evo;

use App\Model\EvoDTO\CalendarDTO;
use App\Model\EvoDTO\FactDTO;
use App\Model\EvoDTO\PlanDTO;
use App\Model\Response\SuccessResponse;
use OpenApi\Annotations as OA;

class InfoEmployerResponse extends SuccessResponse
{
    /**
     * @var float|null
     * @OA\Property(description="Свободный процент", example="10")
     */
    public $freePercent;

    /**
     * @var float|null
     * @OA\Property(description="Свободные часы", example="20")
     */
    public $freeHours;

    /**
     * @var float|null
     * @OA\Property(description="Рабочие дни", example="21")
     */
    public $workingDays;

    /**
     * @var FactDTO[]|null
     * @OA\Property(description="Факт")
     */
    public $fact;

    /**
     * @var PlanDTO[]|null
     * @OA\Property(description="План")
     */
    public $plan;

    /**
     * @var CalendarDTO[]|null
     * @OA\Property(description="Календарь отпусков")
     */
    public $calendar;

    public function __construct(bool $success)
    {
        parent::__construct($success);
    }
}
