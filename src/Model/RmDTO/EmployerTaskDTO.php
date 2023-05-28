<?php

namespace App\Model\RmDTO;

use OpenApi\Annotations as OA;

class EmployerTaskDTO
{
    /**
     * @var int|null
     * @OA\Property(description="id", example="1")
     */
    public ?int $idEmployer;

    /**
     * @var string|null
     * @OA\Property(description="employerName", example="Иванов Иван Иванович")
     *
     */
    public ?string $employerName;

    /**
     * @var SpendingDTO[]|null
     * @OA\Property(description="spending")
     *
     */
    public ?array $spending;
}
