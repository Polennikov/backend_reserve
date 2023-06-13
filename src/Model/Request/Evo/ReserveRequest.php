<?php

namespace App\Model\Request\Evo;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\Groups;
use OpenApi\Annotations as OA;

class ReserveRequest
{
    /**
     * @var int
     * @Serializer\Type("int")
     * @OA\Property(description="Id менеджера", example="14")
     */
    public $manager;

    /**
     * @var int
     * @Serializer\Type("int")
     * @OA\Property(description="Id проекта", example="223")
     */
    public $project;

    /**
     * @var int
     * @Serializer\Type("int")
     * @OA\Property(description="Id пользователя", example="221")
     */
    public $employer;
    /**
     * @var int
     * @Serializer\Type("int")
     * @OA\Property(description="Год", example="2022")
     */
    public $year;

    /**
     * @var int
     * @Serializer\Type("int")
     * @OA\Property(description="Месяц", example="11")
     */
    public $month;

    /**
     * @var float|null
     * @Serializer\Type("float")
     * @OA\Property(description="Процент бронирования", example="25,5")
     */
    public $percent;
}
