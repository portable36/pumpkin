<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportRequested
{
    use Dispatchable, SerializesModels;

    public $reportId;

    public function __construct($reportId)
    {
        $this->reportId = $reportId;
    }
}
