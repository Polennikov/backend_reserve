<?php

namespace App\Model\Request;

use App\Entity\ApprovedPlan;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;

class PlanRequest
{
    /**
     * @var int|null
     * @OA\Property(description="status", example="1")
     */
    public $status;

    /**
     * @var int|null
     * @OA\Property(description="month", example="1")
     */
    public $month;

    /**
     * @var int|null
     * @OA\Property(description="year", example="1")
     */
    public $year;

    /**
     * @var int|null
     * @OA\Property(description="project", example="1")
     */
    public $project;

    /**
     * @var int|null
     * @OA\Property(description="projectName", example="1")
     */
    public $projectName;
}
