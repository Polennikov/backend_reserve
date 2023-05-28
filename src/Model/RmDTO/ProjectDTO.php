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
    public ?int $id;

    /**
     * @var string
     * @OA\Property(description="Название проекта")
     */
    public ?string $name;

    /**
     * @var string|null
     * @OA\Property(description="Идентификатор", example="24")
     */
    public ?string $identifier;

    /**
     * @var string|null
     * @OA\Property(description="Описание", example="25")
     */
    public ?string $description;

    /**
     * @var int|null
     * @OA\Property(description="Статус", example="25")
     */
    public ?int $status;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("is_public")
     * @OA\Property(description="Начало периода", example="01.01.2022")
     */
    public ?string $is_public;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("created_on")
     * @OA\Property(example="01.11.2022")
     */
    public ?string $created_on;

    /**
     * @var string|null
     *
     * @Serializer\Type("string")
     * @Serializer\SerializedName("updated_on")
     * @OA\Property(example="01.11.2022")
     */
    public ?string $updated_on;
}
