<?php

namespace App\Model\Request\Evo;

use JMS\Serializer\Annotation as Serialization;
use OpenApi\Annotations as OA;

class SettingManagerRequest
{
    /**
     * @var int
     * @Serialization\Type("int")
     * @Serialization\SerializedName("countMonth")
     * @OA\Property(description="Кол-во месяцев на вкладке", example="2")
     */
    public $countMonth;

    /**
     * @var string|null
     * @Serialization\Type("string")
     * @Serialization\SerializedName("projectsSidebar")
     * @OA\Property(description="Выбранные проекты ", example="[]")
     */
    public $projectsSidebar;

    /**
     * @var string|null
     * @Serialization\Type("string")
     * @OA\Property(description="Имя менеджера", example="Иван Иванов")
     */
    public $name;
  }
