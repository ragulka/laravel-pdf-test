<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;

class CreatePdfAction
{
    public function execute(string $url)
    {
        Log::debug('Rendering PDF for {url}.', ['url' => $url]);

        $browser = app(Browsershot::class)->setUrl($url);

        // Log console messages
        collect($browser->consoleMessages())->each(function ($entry) {

            if ($entry['type'] === 'error') {
                $location = implode(':', $entry['location']);

                Log::error("Browser: ({$entry['type']}): {$entry['message']} ({$location})");

                return;
            }

            Log::debug("Browser: ({$entry['type']}): {$entry['message']}");
        });

        return $browser->pdf();
    }
}
