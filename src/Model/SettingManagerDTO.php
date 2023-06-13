<?php

namespace App\Model;

use OpenApi\Annotations as OA;

class SettingManagerDTO
{

    /**
     * @var int|null
     * @OA\Property(description="Кол-во отображвемых месяцев", example="1")
     */
    public $countMonth;

    /**
     * @var string|null
     * @OA\Property(description="Кол-во отображвемых месяцев", example="1")
     */
    public $projectsSidebar;

    /**
     * @var string|null
     * @OA\Property(description="ФИО менеджера", example="Иванов Иван Иванович")
     */
    public $name;
}
