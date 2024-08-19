<?php

namespace App\Jobs;

use App\Mail\DailyOrdersReport;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class DailyOrdersJob implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    protected $filePath;
    protected $ordersCount;
    protected $total;

    public $tries = 3; // Maximum number of attempts
    public $backoff = [60, 120, 300]; // Retry intervals in seconds (1 min, 2 min, 5 min)

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $ordersCount, $total)
    {
        $this->filePath = $filePath;
        $this->ordersCount = $ordersCount;
        $this->total = $total;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Mail::to(config('admin.email'))->send(new DailyOrdersReport($this->filePath, $this->ordersCount, $this->total));
        } catch (\Exception $e) {
            Log::error('Failed to send daily orders report email: ' . $e->getMessage());

            // Re-throw the exception to trigger the retry mechanism
            throw $e;
        }
    }

}
