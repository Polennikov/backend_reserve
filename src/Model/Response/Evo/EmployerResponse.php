<?php

namespace App\Model\Response\Evo;

use App\Model\EvoDTO\EmployerDTO;
use App\Model\Response\SuccessResponse;

class EmployerResponse extends SuccessResponse
{
    /**
     * @var EmployerDTO[]
     */
    public $employers;
}
