<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Context7CheckCommand extends Command
{
    protected $signature = 'context7:check';

    protected $description = 'Run Context7 compliance checks (stub)';

    public function handle(): int
    {
        $this->info('Context7 compliance check executed');
        return self::SUCCESS;
    }
}

