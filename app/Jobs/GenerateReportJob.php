<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateReportJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $reportId;

    public function __construct($reportId)
    {
        $this->reportId = $reportId;
    }

    public function handle()
    {
        Log::info('GenerateReportJob running', ['report_id' => $this->reportId]);

        try {
            $html = "<html><body><h1>Report #{$this->reportId}</h1><p>Generated at: " . now() . "</p></body></html>";

            // Use Dompdf to render PDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $output = $dompdf->output();
            $path = storage_path("app/reports/report-{$this->reportId}.pdf");
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path), 0755, true);
            }
            file_put_contents($path, $output);

            Log::info('Report generated', ['path' => $path]);
        } catch (\Exception $e) {
            Log::error('Report generation failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
