<?php

namespace Didix16\LaraPrime\Commands;

use Illuminate\Console\Command;

class LaraPrimeCommand extends Command
{
    public $signature = 'laraprime';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
