<?php

namespace App\Model\Request\Rm;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class TaskEditRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     * @Serializer\SerializedName("taskId")
     */
    public string $taskId;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     * @Serializer\SerializedName("reportId")
     */
    public string $reportId;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("reportId")
     */
    public string $projectEvo;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     * @Serializer\SerializedName("dateTo")
     */
    public string $dateTo;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     * @Serializer\SerializedName("dateFrom")
     */
    public string $dateFrom;
}