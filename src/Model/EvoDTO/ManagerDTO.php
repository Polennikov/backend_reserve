<?php

namespace App\Model\EvoDTO;

use OpenApi\Annotations as OA;

class ManagerDTO
{
    /**
     * @var int|null
     * @OA\Property(description="id", example="1")
     */
    public $id;

    /**
     * @var string|null
     * @OA\Property(description="login",example="login@mail.ru")
     */
    public $login;

    /**
     * @var string|null
     * @OA\Property(description="ФИО", example="Иванов Иван Иванович")
     */
    public $managerName;
}
