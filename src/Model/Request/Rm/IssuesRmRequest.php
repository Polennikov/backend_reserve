<?php

namespace App\Model\Request\Rm;

use App\Model\RmDTO\FiltersIssueDTO;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class IssuesRmRequest
{
    /**
     * @Serializer\Type("string")
     * @OA\Property(description="id Проекта RM", example="123")
     * @Serializer\SerializedName("project")
     */
    public string $project;

    /**
     * @var FiltersIssueDTO $filter
     * @Serializer\SerializedName("filter")
     */
    public FiltersIssueDTO $filter;

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('project', new Assert\NotBlank());
        $metadata->addPropertyConstraint('filter', new Assert\Valid());
    }
}