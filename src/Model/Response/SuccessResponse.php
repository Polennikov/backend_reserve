<?php

namespace App\Model\Response;

use JMS\Serializer\Annotation as Serializer;

class SuccessResponse
{
    /**
     * @var bool
     * @Serializer\Groups({"default"})
     */
    public bool $success;

    /**
     * SuccessResponse constructor.
     *
     * @param bool $success
     */
    public function __construct(bool $success = true)
    {
        $this->success = $success;
    }
}
