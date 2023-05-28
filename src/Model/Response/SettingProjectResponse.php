<?php

namespace App\Model\Response;

use App\Model\SettingProjectDTO;

class SettingProjectResponse extends SuccessResponse
{
    /**
     * @var SettingProjectDTO
     */
    public SettingProjectDTO $setting;

    public function __construct(bool $success, SettingProjectDTO $setting)
    {
        parent::__construct($success);
        $this->setting = $setting;
    }
}
