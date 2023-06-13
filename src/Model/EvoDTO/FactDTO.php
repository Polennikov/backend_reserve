<?php

namespace App\Model\EvoDTO;

use OpenApi\Annotations as OA;

class FactDTO
{

    /**
     * @var int|null
     * @OA\Property(description="Project/Employer id", example="1")
     */
    public $id;

    /**
     * @var float|null
     * @OA\Property(description="Часы", example="25,5")
     */
    public $hour;
}
