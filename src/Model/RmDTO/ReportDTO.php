<?php

namespace App\Model\RmDTO;

use OpenApi\Annotations as OA;

class ReportDTO
{
    /**
     * @var int|null
     * @OA\Property(description="id", example="1")
     */
    public $idTask;

    /**
     * @var EmployerTaskDTO[]|null
     * @OA\Property(description="Задачи")
     *
     */
    public $employerTask;

    /**
     * @var string|null
     * @OA\Property(description="Всего часов Rm", example="24")
     */
    public $hoursAllRm;

    /**
     * @var string|null
     * @OA\Property(description="Всего часов Evo", example="25")
     */
    public $hoursAllEvo;

    /**
     * @var string|null
     * @OA\Property(description="Оценка RM", example="25")
     */
    public $redmineEstimate;

    /**
     * @var string|null
     * @OA\Property(description="Начало периода", example="01.01.2022")
     */
    public $dateFrom;

    /**
     * @var string|null
     * @OA\Property(description="Конец периода", example="01.11.2022")
     */
    public $dateTo;
}
