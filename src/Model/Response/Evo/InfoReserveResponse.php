<?php

namespace App\Model\Response\Evo;

use App\Model\EvoDTO\CalendarDTO;
use App\Model\EvoDTO\ReserveDTO;
use App\Model\Response\SuccessResponse;
use OpenApi\Annotations as OA;

class InfoReserveResponse extends SuccessResponse
{
    /**
     * @var float|null
     * @OA\Property(description="Свободный процент", example="10")
     */
    public ?float $freePercent;

    /**
     * @var float|null
     * @OA\Property(description="Свободные часы", example="20")
     */
    public ?float $freeHours;

    /**
     * @var float|null
     * @OA\Property(description="Занятый процент", example="90")
     */
    public ?float $allReservePercent;

    /**
     * @var float|null
     * @OA\Property(description="Кол-во дней отпуска", example="14")
     */
    public ?float $absentDays;

    /**
     * @var float|null
     * @OA\Property(description="Текущий процент менеджера", example="5")
     */
    public ?float $managerReservePercent;

    /**
     * @var CalendarDTO[]|null
     * @OA\Property(description="Календарь отпусков")
     */
    public ?array $calendar;

    public function __construct(bool $success)
    {
        parent::__construct($success);
    }
}
