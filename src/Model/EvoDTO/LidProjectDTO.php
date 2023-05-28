<?php

namespace App\Model\EvoDTO;

use OpenApi\Annotations as OA;

class LidProjectDTO
{

    /**
     * @var int|null
     * @OA\Property(description="Кол-во отображвемых месяцев", example="1")
     */
    public ?int $hoursWork;
}
