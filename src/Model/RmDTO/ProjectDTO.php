<?php

namespace App\Model\RmDTO;

use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;

class ProjectDTO
{
    /**
     * @var int|null
     * @OA\Property(description="id", example="1")
     */
    public $id;

    /**
     * @var string
     * @OA\Property(description="Название проекта")
     */
    public $name;

    /**
     * @var string|null
     * @OA\Property(description="Идентификатор", example="24")
     */
    public $identifier;

    /**
     * @var string|null
     * @OA\Property(description="Описание", example="25")
     */
    public $description;

    /**
     * @var int|null
     * @OA\Property(description="Статус", example="25")
     */
    public $status;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("is_public")
     * @OA\Property(description="Начало периода", example="01.01.2022")
     */
    public $is_public;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("created_on")
     * @OA\Property(example="01.11.2022")
     */
    public $created_on;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("updated_on")
     * @OA\Property(example="01.11.2022")
     */
    public $updated_on;
}
