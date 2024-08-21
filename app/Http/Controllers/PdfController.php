<?php

namespace App\Http\Controllers;

use App\Actions\CreatePdfAction;
use App\Jobs\GeneratePdfJob;
use Exception;
use Illuminate\Support\Facades\Cache;

class PdfController extends Controller
{
    /**
     * @throws Exception
     */
    public function direct()
    {
        $pdf = app(CreatePdfAction::class)->execute('https://laravel.com');

        return $this->download($pdf);
    }

    /**
     * @throws Exception
     */
    public function queued()
    {
        $url = 'https://laravel.com';

        GeneratePdfJob::dispatch($url)->onQueue('pdf');

        // Define a maximum generation time in seconds
        $maxGenerationTime = 15; // 15 seconds

        // Track the start time
        $startTime = microtime(true);

        // Poll for the job completion via cache
        $cacheKey = 'temp_pdf_'.base64_encode($url);

        while (! Cache::has($cacheKey)) {

            // Check if the elapsed time exceeds the maximum generation time
            if ((microtime(true) - $startTime) > $maxGenerationTime) {
                throw new Exception('PDF generation timed out.');
            }

            usleep(50000); // Wait for 50ms before checking again
        }

        $pdf = Cache::get($cacheKey);

        Cache::forget($cacheKey);

        return $this->download($pdf);
    }

    /**
     * @throws Exception
     */
    protected function download(string $pdf)
    {
        if (empty($pdf)) {
            throw new Exception('PDF content is empty.');
        }

        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="pdftest.pdf"',
        ];

        return response($pdf, 200, $headers);

    }
}
