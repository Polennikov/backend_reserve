<?php

namespace App\Model\Response;

use App\Model\SettingEmployerDTO;

class SettingEmployerResponse extends SuccessResponse
{
    /**
     * @var SettingEmployerDTO
     */
    public $setting;

    public function __construct(bool $success, SettingEmployerDTO $setting)
    {
        parent::__construct($success);
        $this->setting = $setting;
    }
}
