<?php

namespace App\Model;

use OpenApi\Annotations as OA;

class SettingEmployerDTO
{

    /**
     * @var int|null
     * @OA\Property(description="Кол-во отображвемых месяцев", example="1")
     */
    public $hoursWork;
}
