<?php

namespace App\Model\EvoDTO;

use App\Model\SettingEmployerDTO;
use App\Model\SettingProjectDTO;
use OpenApi\Annotations as OA;

class EmployerDTO
{
    /**
     * @var int|null
     * @OA\Property(description="id", example="1")
     */
    public $id;

    /**
     * @var string|null
     * @OA\Property(description="ФИО",example="Иванов Иван Иванович")
     */
    public $title;

    /**
     * @var string|null
     * @OA\Property(description="Грейд", example="Junior")
     *
     */
    public $grade;

    /**
     * @var string|null
     * @OA\Property(description="Компетенция", example="Дизайнер")
     */
    public $competence;

    /**
     * @var string|null
     * @OA\Property(description="Трудоустройство", example="Штат")
     */
    public $employment;

    /**
     * @var string|null
     * @OA\Property(description="Компания", example="Интаро")
     */
    public $company;

    /**
     * @var string|null
     * @OA\Property(description="Дата увольнения", example="04.01.1993")
     */
    public $dateEnd;

    /**
     * @var string|null
     * @OA\Property(description="Часы работы в Intaro", example="4")
     */
    public $intaroHours;

    /**
     * @var int|null
     * @OA\Property(description="id из Bitrix 22", example="123")
     */
    public $idB24;

    /**
     * @var SettingEmployerDTO|null
     * @OA\Property(description="Настройки пользователя")
     */
    public $settings;

    /**
     * @var SettingProjectDTO[]|null
     * @OA\Property(description="Проекты техлидство")
     */
    public $lidProjectId;
}
