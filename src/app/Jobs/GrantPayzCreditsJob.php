<?php
namespace App\Jobs;

use App\Services\PayzCreditGrantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GrantPayzCreditsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Log::info('GrantPayzCreditsJob: start');
        $service = new PayzCreditGrantService();
        $result = $service->run();
        Log::info('GrantPayzCreditsJob: end', ['result' => $result]);
    }
}