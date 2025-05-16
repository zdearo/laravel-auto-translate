<?php

namespace Zdearo\LaravelAutoTranslate\Commands;

use Illuminate\Console\Command;

class LaravelAutoTranslateCommand extends Command
{
    public $signature = 'laravel-auto-translate';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
