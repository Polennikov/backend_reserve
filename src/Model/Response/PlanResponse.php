<?php

namespace App\Model\Response;

use App\Entity\ApprovedPlan;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;

class PlanResponse extends SuccessResponse
{
    /**
     * @var string|null
     * @OA\Property(description="status", example="1")
     */
    public $status;

    /**
     * @var string|null
     * @OA\Property(description="month", example="1")
     */
    public $month;

    /**
     * @var string|null
     * @OA\Property(description="year", example="1")
     */
    public $year;

    /**
     * @var string|null
     * @OA\Property(description="project", example="1")
     */
    public $project;

    /**
     * @var string|null
     * @OA\Property(description="projectName", example="1")
     */
    public $projectName;

    /**
     * @var ApprovedPlanResponse[]|null
     * @Serializer\SerializedName("approvedPlan")
     */
    public $approvedPlan;
}
