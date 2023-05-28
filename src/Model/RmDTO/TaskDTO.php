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
    public ?int $id;

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
    public ?string $subject;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("description")
     */
    public ?string $description;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("start_date")
     */
    public ?string $start_date;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("due_date")
     */
    public ?string $due_date;

    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     * @Serializer\SerializedName("done_ratio")
     */
    public ?int $done_ratio;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("is_private")
     */
    public ?string $is_private;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("estimated_hours")
     */
    public ?string $estimated_hours;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("created_on")
     */
    public ?string $created_on;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("updated_on")
     */
    public ?string $updated_on;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("closed_on")
     */
    public ?string $closed_on;
}
