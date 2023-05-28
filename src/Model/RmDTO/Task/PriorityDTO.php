<?php

namespace App\Model\RmDTO\Task;

use JMS\Serializer\Annotation as Serializer;

class PriorityDTO
{
    /**
     * @var int|null
     *
     * @Serializer\Type("int")
     * @Serializer\SerializedName("id")
     */
    public $id;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("name")
     */
    public $name;
}
