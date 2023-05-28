<?php

namespace App\Model\RmDTO;

use OpenApi\Annotations as OA;

class ReportDTO
{
    /**
     * @var int|null
     * @OA\Property(description="id", example="1")
     */
    public ?int $idTask;

    /**
     * @var EmployerTaskDTO[]|null
     * @OA\Property(description="Задачи")
     *
     */
    public ?array $employerTask;

    /**
     * @var string|null
     * @OA\Property(description="Всего часов Rm", example="24")
     */
    public ?string $hoursAllRm;

    /**
     * @var string|null
     * @OA\Property(description="Всего часов Evo", example="25")
     */
    public ?string $hoursAllEvo;

    /**
     * @var string|null
     * @OA\Property(description="Оценка RM", example="25")
     */
    public ?string $redmineEstimate;

    /**
     * @var string|null
     * @OA\Property(description="Начало периода", example="01.01.2022")
     */
    public ?string $dateFrom;

    /**
     * @var string|null
     * @OA\Property(description="Конец периода", example="01.11.2022")
     */
    public ?string $dateTo;
}
