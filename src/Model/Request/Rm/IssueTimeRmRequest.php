<?php

namespace App\Model\Request\Rm;

use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;

class IssueTimeRmRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     * @OA\Property(description="Auth token", example="j75y4htrg34grfcw5hyv45")
     * @Serializer\SerializedName("token")
     */
    public $token;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     * @OA\Property(description="id Проекта RM", example="123")
     * @Serializer\SerializedName("project")
     */
    public $project;
}
