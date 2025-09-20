<?php

namespace Nanorocks\FilamentActivityHistory\Commands;

use Illuminate\Console\Command;

class FilamentActivityHistoryCommand extends Command
{
    public $signature = 'filament-activity-history';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
