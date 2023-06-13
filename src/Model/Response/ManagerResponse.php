<?php

namespace App\Model\Response;

use App\Model\EvoDTO\ManagerDTO;

class ManagerResponse extends SuccessResponse
{
    /**
     * @var ManagerDTO[]|null
     */
    public $manager;

    public function __construct(bool $success, array $manager)
    {
        parent::__construct($success);
        $this->manager = $manager;
    }
}
