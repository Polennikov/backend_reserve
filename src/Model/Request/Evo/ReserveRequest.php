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
    public int $manager;

    /**
     * @var int
     * @Serializer\Type("int")
     * @OA\Property(description="Id проекта", example="223")
     */
    public int $project;

    /**
     * @var int
     * @Serializer\Type("int")
     * @OA\Property(description="Id пользователя", example="221")
     */
    public int $employer;
    /**
     * @var int
     * @Serializer\Type("int")
     * @OA\Property(description="Год", example="2022")
     */
    public int $year;

    /**
     * @var int
     * @Serializer\Type("int")
     * @OA\Property(description="Месяц", example="11")
     */
    public int $month;

    /**
     * @var float|null
     * @Serializer\Type("float")
     * @OA\Property(description="Процент бронирования", example="25,5")
     */
    public ?float $percent;
}
