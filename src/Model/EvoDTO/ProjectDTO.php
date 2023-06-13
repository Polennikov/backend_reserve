<?php

namespace App\Model\EvoDTO;

use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations as OA;

class ProjectDTO
{
    /**
     * @var int|null
     * @OA\Property(description="id", example="1")
     */
    public $id;

    /**
     * @var string|null
     * @OA\Property(description="Название",example="Acer")
     */
    public $title;

    /**
     * @var string|null
     * @OA\Property(description="redmine_url", example="...")
     *
     */
    public $redmine_url;

    /**
     * @var string|null
     * @OA\Property(description="Статус", example="завершение")
     */
    public $status;
}
