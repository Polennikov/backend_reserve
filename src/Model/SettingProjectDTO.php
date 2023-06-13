<?php

namespace App\Model;

use OpenApi\Annotations as OA;

class SettingProjectDTO
{
    /**
     * @var int|null
     * @OA\Property(description="id", example="1")
     */
    public $id;

    /**
     * @var int|null
     * @OA\Property(description="id Техлида",example="123")
     */
    public $lidId;

    /**
     * @var int|null
     * @OA\Property(description="id Проекта", example="321")
     *
     */
    public $projectId;
}
