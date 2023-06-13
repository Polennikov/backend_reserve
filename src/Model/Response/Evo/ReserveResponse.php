<?php

namespace App\Model\Response\Evo;

use App\Model\EvoDTO\ReserveDTO;
use App\Model\Response\SuccessResponse;

class ReserveResponse extends SuccessResponse
{
    /**
     * @var ReserveDTO[]
     */
    public $reserve;

    public function __construct(bool $success, array $reserve)
    {
        parent::__construct($success);
        $this->reserve = $reserve;
    }
}
