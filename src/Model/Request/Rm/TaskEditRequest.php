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
    public $taskId;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     * @Serializer\SerializedName("reportId")
     */
    public $reportId;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Serializer\SerializedName("reportId")
     */
    public $projectEvo;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     * @Serializer\SerializedName("dateTo")
     */
    public $dateTo;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Serializer\Type("string")
     * @Serializer\SerializedName("dateFrom")
     */
    public $dateFrom;
}
