<?php

namespace App\Model\EvoDTO;

use OpenApi\Annotations as OA;

class ReserveDTO
{

    /**
     * @var int|null
     * @OA\Property(description="Месяц", example="11")
     */
    public ?int $month;

    /**
     * @var int|null
     * @OA\Property(description="Год", example="2022")
     */
    public ?int $year;

    /**
     * @var FactDTO[]|null
     * @OA\Property(description="Факт")
     */
    public ?array $fact;

    /**
     * @var PlanDTO[]|null
     * @OA\Property(description="План (бронь)")
     */
    public ?array $plan;
}
