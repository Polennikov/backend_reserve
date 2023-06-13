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
    public $month;

    /**
     * @var string|null
     * @OA\Property(description="year", example="2022")
     *
     */
    public $year;

    /**
     * @var string|null
     * @OA\Property(description="hoursEvo", example="13")
     *
     */
    public $hoursEvo;

    /**
     * @var string|null
     * @OA\Property(description="hoursRm", example="12")
     *
     */
    public $hoursRm;

    /**
     * @var string|null
     * @OA\Property(description="employerName", example="Иванов Иван Иванович")
     *
     */
    public $employerName;

    /**
     * @var string|null
     * @OA\Property(description="employerId", example="12")
     *
     */
    public $employerId;
}
