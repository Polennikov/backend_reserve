<?php

namespace App\Model\Response;

use App\Model\SettingManagerDTO;

class SettingManagerResponse extends SuccessResponse
{
    /**
     * @var SettingManagerDTO|null
     */
    public $setting;

    public function __construct(bool $success, SettingManagerDTO $setting)
    {
        parent::__construct($success);
        $this->setting = $setting;
    }
}
