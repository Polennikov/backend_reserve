<?php

namespace App\Model\EvoDTO;

use OpenApi\Annotations as OA;

class CalendarDTO
{

    /**
     * @var int|null
     * @OA\Property(description="Employer id", example="1")
     */
    public $id;

    /**
     * @var string|null
     * @OA\Property(description="Дата начала", example="28.11.2022")
     */
    public $dateFrom;

    /**
     * @var string|null
     * @OA\Property(description="Дата конца", example="11.12.2022")
     */
    public $dateTo;

    /**
     * @var string|null
     * @OA\Property(description="Признак", example="absent")
     */
    public $accessibilty;
}
