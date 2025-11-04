<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TCMBCurrencyService;

/**
 * Update Exchange Rates Command
 * 
 * Context7: Daily scheduled task to update TCMB currency rates
 * Usage: php artisan exchange:update
 * Schedule: Daily at 10:00 AM (after TCMB publishes)
 */
class UpdateExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange:update
                            {--force : Force update even if already updated today}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update exchange rates from TCMB (Türkiye Cumhuriyet Merkez Bankası)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Updating exchange rates from TCMB...');
        $this->newLine();
        
        try {
            $service = new TCMBCurrencyService();
            
            // Check if --force flag is used
            if (!$this->option('force')) {
                // Check if already updated today
                $rates = $service->getTodayRates();
                if ($rates && count($rates) > 0) {
                    $this->warn('⚠️  Rates already updated today. Use --force to update anyway.');
                    $this->displayRates($rates);
                    return 0;
                }
            }
            
            $this->info('📡 Fetching rates from TCMB...');
            
            with($this->output->createProgressBar(7), function ($bar) use ($service) {
                $bar->start();
                $updated = $service->updateRates();
                $bar->finish();
                
                $this->newLine(2);
                
                if ($updated > 0) {
                    $this->info("✅ Successfully updated {$updated} exchange rates!");
                    
                    // Display updated rates
                    $this->displayRates($service->getTodayRates());
                } else {
                    $this->error('❌ No rates were updated. Check logs for details.');
                    return 1;
                }
            });
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error updating exchange rates:');
            $this->error($e->getMessage());
            return 1;
        }
    }
    
    /**
     * Display rates in a table
     * 
     * @param array $rates
     */
    private function displayRates(array $rates)
    {
        if (empty($rates)) {
            return;
        }
        
        $this->newLine();
        $this->info('💱 Current Exchange Rates:');
        $this->table(
            ['Currency', 'Buying', 'Selling', 'Date', 'Source'],
            collect($rates)->map(function ($rate) {
                return [
                    $rate['code'],
                    number_format($rate['forex_buying'], 4),
                    number_format($rate['forex_selling'], 4),
                    $rate['date'],
                    $rate['source']
                ];
            })->toArray()
        );
    }
}
