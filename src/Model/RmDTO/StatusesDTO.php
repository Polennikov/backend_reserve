<?php

namespace App\Model\RmDTO;

use JMS\Serializer\Annotation as Serializer;

class StatusesDTO
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

    /**
     * @var bool|null
     *
     * @Serializer\Type("bool")
     * @Serializer\SerializedName("is_closed")
     */
    public $isClosed;
}