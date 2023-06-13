<?php

namespace App\Model\RmDTO;

use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class FiltersIssueDTO
{
    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("monthPaid")
     * @OA\Property(description="Дата оплаты", example="Март 2022")
     */
    public $monthPaid;

    /**
     * @var int[]|null
     * @Serializer\Type("array")
     * @Serializer\SerializedName("statuses")
     * @OA\Property(description="Статусы проекта", example="[1, 2, 3]")
     */
    public $statuses;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("dateFrom")
     * @OA\Property(description="Начальная дата", example="2022-10-29")
     */
    public $dateFrom;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("dateTo")
     * @OA\Property(description="Конечная дата", example="2022-12-29")
     */
    public $dateTo;

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('dateFrom', new Assert\NotBlank());
        $metadata->addPropertyConstraint('dateTo', new Assert\NotBlank());
    }
}
