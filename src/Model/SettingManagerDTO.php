<?php

namespace App\Model;

use OpenApi\Annotations as OA;

class SettingManagerDTO
{

    /**
     * @var int|null
     * @OA\Property(description="Кол-во отображвемых месяцев", example="1")
     */
    public ?int $countMonth;

    /**
     * @var string|null
     * @OA\Property(description="Кол-во отображвемых месяцев", example="1")
     */
    public ?string $projectsSidebar;

    /**
     * @var string|null
     * @OA\Property(description="ФИО менеджера", example="Иванов Иван Иванович")
     */
    public ?string $name;
}
