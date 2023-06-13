<?php

namespace App\Model\Response;


use OpenApi\Annotations as OA;

class BadResponse
{
    /**
     * @var bool
     * @OA\Property(example="false")
     */
    public $success;

    /**
     * @var string
     * @OA\Property(example="Ошибка...")
     */
    public $message;

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
