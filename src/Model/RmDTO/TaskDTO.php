<?php

namespace App\Model\RmDTO;

use App\Model\RmDTO\Task\AssignedDTO;
use App\Model\RmDTO\Task\AuthorDTO;
use App\Model\RmDTO\Task\PriorityDTO;
use App\Model\RmDTO\Task\ProjectDTO;
use App\Model\RmDTO\Task\StatusDTO;
use App\Model\RmDTO\Task\TrackerDTO;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;

class TaskDTO
{
    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     * @Serializer\SerializedName("id")
     */
    public $id;

    /**
     * @var ProjectDTO|null
     *
     * @Serializer\Type("App\Model\RmDTO\Task\ProjectDTO")
     * @Serializer\SerializedName("project")
     */
    public $project;

    /**
     * @var TrackerDTO|null
     *
     * @Serializer\Type("App\Model\RmDTO\Task\TrackerDTO")
     * @Serializer\SerializedName("tracker")
     */
    public $tracker;

    /**
     * @var StatusDTO|null
     *
     * @Serializer\Type("App\Model\RmDTO\Task\StatusDTO")
     * @Serializer\SerializedName("status")
     */
    public $status;

    /**
     * @var PriorityDTO|null
     *
     * @Serializer\Type("App\Model\RmDTO\Task\PriorityDTO")
     * @Serializer\SerializedName("priority")
     */
    public $priority;

    /**
     * @var AuthorDTO|null
     *
     * @Serializer\Type("App\Model\RmDTO\Task\AuthorDTO")
     * @Serializer\SerializedName("author")
     */
    public $author;

    /**
     * @var AssignedDTO|null
     *
     * @Serializer\Type("App\Model\RmDTO\Task\AssignedDTO")
     * @Serializer\SerializedName("assigned_to")
     */
    public $assignedTo;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("subject")
     */
    public $subject;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("description")
     */
    public $description;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("start_date")
     */
    public $start_date;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("due_date")
     */
    public $due_date;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     * @Serializer\SerializedName("done_ratio")
     */
    public $done_ratio;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("is_private")
     */
    public $is_private;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("estimated_hours")
     */
    public $estimated_hours;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("created_on")
     */
    public $created_on;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("updated_on")
     */
    public $updated_on;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("closed_on")
     */
    public $closed_on;
}
