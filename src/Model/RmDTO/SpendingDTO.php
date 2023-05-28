<?php

namespace App\Model\RmDTO;

use OpenApi\Annotations as OA;

class SpendingDTO
{
    /**
     * @var string|null
     * @OA\Property(description="month", example="11")
     *
     */
    public ?string $month;

    /**
     * @var string|null
     * @OA\Property(description="year", example="2022")
     *
     */
    public ?string $year;

    /**
     * @var string|null
     * @OA\Property(description="hoursEvo", example="13")
     *
     */
    public ?string $hoursEvo;

    /**
     * @var string|null
     * @OA\Property(description="hoursRm", example="12")
     *
     */
    public ?string $hoursRm;

    /**
     * @var string|null
     * @OA\Property(description="employerName", example="Иванов Иван Иванович")
     *
     */
    public ?string $employerName;

    /**
     * @var string|null
     * @OA\Property(description="employerId", example="12")
     *
     */
    public ?string $employerId;
}