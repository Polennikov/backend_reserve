<?php

namespace App\Model\RmDTO\Task;

use JMS\Serializer\Annotation as Serializer;

class AssignedDTO
{
    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     * @Serializer\SerializedName("id")
     */
    public ?int $id;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("name")
     */
    public ?string $name;
}
