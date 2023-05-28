<?php

namespace App\Model\Response;


use OpenApi\Annotations as OA;

class BadResponse
{
    /**
     * @var bool
     * @OA\Property(example="false")
     */
    public bool $success;

    /**
     * @var string
     * @OA\Property(example="Ошибка...")
     */
    public string $message;

    /**
     * BadResponse constructor.
     *
     * @param bool $success
     * @param string $message
     */
    public function __construct(bool $success, string $message)
    {
        $this->success = $success;
        $this->message = $message;
    }
}
