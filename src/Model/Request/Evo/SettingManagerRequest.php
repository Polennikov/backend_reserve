<?php

namespace App\Model\Request\Evo;

use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;
class SettingManagerRequest
{
    /**
     * @var int
     * @Serializer\Type("int")
     * @Serializer\SerializedName("countMonth")
     * @OA\Property(description="Кол-во месяцев на вкладке", example="2")
     */
    public int $countMonth;

    /**
     * @var string|null
     * @Serializer\Type("string")
     * @Serializer\SerializedName("projectsSidebar")
     * @OA\Property(description="Выбранные проекты ", example="[]")
     */
    public ?string $projectsSidebar;

    /**
     * @var string|null
     * @Serializer\Type("string")
     * @OA\Property(description="Имя менеджера", example="Иван Иванов")
     */
    public ?string $name;
  }
