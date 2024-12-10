<?php

namespace Didix16\LaraPrime\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'laraprime:publish')]
class PublishCommand extends Command
{
    /**
     * The console command signature.
     * @var string
     */
    protected $signature = 'laraprime:publish';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Publish all compiled LaraPrime assets';

    /**
     * Execute the console command.
     * @return void
     */
    public function handle(): void
    {
        $this->call('vendor:publish', [
            '--tag' => 'laraprime-assets',
            '--force' => true
        ]);
    }
}
