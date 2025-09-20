<?php

namespace Nanorocks\FilamentActivityHistory\Commands;

use Illuminate\Console\Command;

/**
 * Command to clean the activity log using the 'activitylog:clean' Artisan command.
 *
 * This command is intended to clear the activity log and provide a confirmation message upon completion.
 *
 * @command filament-activity-history
 * @description My command
 *
 * add comment here for clear activity log
 */
use Illuminate\Support\Facades\Artisan;

class FilamentActivityHistoryClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'filament-activity-history-clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean the activity log using activitylog:clean';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $exitCode = Artisan::call('activitylog:clean');

        $this->comment('All done');

        return $exitCode;
    }
}
