<?php

namespace App\Model\EvoDTO;

use OpenApi\Annotations as OA;

class HistoryDTO
{

    /**
     * @var int|null
     * @OA\Property(description="Новое значение", example="10")
     */
    public $new;

    /**
     * @var int|null
     * @OA\Property(description="Старое значение", example="20")
     */
    public $old;

    /**
     * @var string|null
     * @OA\Property(description="Дата", example="24.11.2022 16:26:56")
     */
    public $date;
}
