<?php

namespace App\Model;

use JMS\Serializer\Annotation as Serialization;
use OpenApi\Annotations as OA;

class AuthDTO
{
    /**
     * @var string|null
     * @Serialization\Type("string")
     * @OA\Property(description="Логин", example="login")
     */
    public $login;

    /**
     * @var string|null
     * @Serialization\Type("string")
     * @OA\Property(description="Пароль",example="password")
     */
    public $password;

    /**
     * @var string|null
     * @OA\Property(description="Токен", example="token")
     *
     */
    public $token;
}
