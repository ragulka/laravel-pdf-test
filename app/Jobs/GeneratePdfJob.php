<?php

namespace App\Jobs;

use App\Actions\CreatePdfAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class GeneratePdfJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $url)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $lockKey = 'lock_pdf_'.$this->url;
        $cacheKey = 'temp_pdf_'.$this->url;

        // only allow a single job to generate the PDF at a time
        Cache::lock($lockKey, 10)->block(1, function () use ($cacheKey) {

            Cache::put(
                $cacheKey,

                app(CreatePdfAction::class)->execute($this->url),

                now()->addMinutes(5)
            );
        });
    }
}
