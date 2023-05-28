<?php

namespace App\Model\EvoDTO;

use OpenApi\Annotations as OA;

class PlanResourceDTO
{

    /**
     * @var int|null
     * @OA\Property(description="Employer id", example="1")
     */
    public ?int $id;

    /**
     * @var float|null
     * @OA\Property(description="Процент", example="25,5")
     */
    public ?float $percent;
}
