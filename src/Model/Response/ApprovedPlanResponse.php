<?php

namespace App\Model\Response;

use App\Entity\ApprovedPlan;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;

class ApprovedPlanResponse
{
    /**
     * @var int|null
     * @OA\Property(description="competence", example="1")
     */
    public $competence;

    /**
     * @var int|null
     * @OA\Property(description="plan", example="1")
     */
    public $plan;

    /**
     * @var string|null
     * @OA\Property(description="value", example="1")
     */
    public $value;
}
