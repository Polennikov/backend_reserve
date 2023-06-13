<?php

namespace App\Model\EvoDTO;

use OpenApi\Annotations as OA;

class PlanDTO
{

    /**
     * @var float|null
     * @OA\Property(description="Процент", example="11,5")
     */
    public $percent;

    /**
     * @var int|null
     * @OA\Property(description="Проект", example="111")
     */
    public $project;

    /**
     * @var string|null
     * @OA\Property(description="Дата резерва", example="2022.10.24")
     */
    public $dateReserve;

    /**
     * @var HistoryDTO[]|null
     * @OA\Property(description="История брони")
     */
    public $history;

    /**
     * @var int|null
     * @OA\Property(description="id Менеджера")
     */
    public $manager;

    /**
     * @var int|null
     * @OA\Property(description="id Сотрудника")
     */
    public $employer;
}
