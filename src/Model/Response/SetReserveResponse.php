<?php

namespace App\Model\Response;

use App\Model\EvoDTO\HistoryDTO;
use OpenApi\Annotations as OA;

class SetReserveResponse extends SuccessResponse
{
    /**
     * @var float|null
     * @OA\Property(description="Процент", example="10")
     */
    public ?float $percent;

    /**
     * @var HistoryDTO[]|null
     * @OA\Property(description="История")
     */
    public ?array $history;

    public function __construct(bool $success, float $percent, array $history)
    {
        parent::__construct($success);
        $this->percent = $percent;
        $this->history = $history;
    }
}
