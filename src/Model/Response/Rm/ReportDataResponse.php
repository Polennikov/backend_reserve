<?php

namespace App\Model\Response\Rm;

use App\Model\Response\SuccessResponse;
use App\Model\RmDTO\ReportDTO;

class ReportDataResponse extends SuccessResponse
{
    /**
     * @var ReportDTO[]
     */
    public array $report;

    /**
     * @var string[]|null
     */
    public array $dateInterval;

    public function __construct(bool $success, array $report, $dateInterval)
    {
        parent::__construct($success);
        $this->report = $report;
        $this->dateInterval = $dateInterval;
    }
}
