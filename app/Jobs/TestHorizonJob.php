<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TestHorizonJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $message;

    /**
     * Create a new job instance.
     */
    public function __construct($message = 'Test job çalışıyor!')
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Simulate processing time
        sleep(2);
        
        Log::info('Horizon Test Job executed: ' . $this->message);
        
        // Simulate some work
        $result = "Job completed at " . now()->format('Y-m-d H:i:s');
        
        Log::info('Result: ' . $result);
    }
}

