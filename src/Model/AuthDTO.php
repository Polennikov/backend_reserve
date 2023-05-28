<?php

namespace App\Model;

use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;

class AuthDTO
{
    /**
     * @var string|null
     * @OA\Property(description="Логин", example="login")
     */
    public ?string $login;

    /**
     * @var string|null
     * @OA\Property(description="Пароль",example="password")
     */
    public ?string $password;

    /**
     * @var string|null
     * @OA\Property(description="Токен", example="token")
     *
     */
    public ?string $token;
}
