<?php

namespace App\Model\Response;

class SuccessResponse
{
    /**
     * @var bool
     */
    public $success;

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
