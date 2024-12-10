<?php

namespace Didix16\LaraPrime\Commands;

use Didix16\LaraPrime\LaraPrime;
use Didix16\LaraPrime\LaraPrimeServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Traits\Conditionable;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'laraprime:install')]
class InstallCommand extends Command
{
    use Conditionable;

    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'laraprime:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the LaraPrime files';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->comment('Installation started. Please wait...');
        $this->info('Version: '.LaraPrime::version());

        $this
            ->executeCommand('vendor:publish', [
                '--provider' => LaraPrimeServiceProvider::class,
                '--tag' => [
                    'laraprime-config',
                    'laraprime-migrations',
                    'laraprime-app-stubs',
                    'laraprime-assets',
                ],
            ])
            ->executeCommand('migrate')
            /*->when(class_exists(\App\Models\User::class), function () {
                $this->replaceInFiles(app_path(), 'use Didix16\\LaraPrime\\Models\\User;', 'use App\\Models\\User;');
            });*/
            ->showMeLove();

        $this->info('Completed!');
        $this->comment('If you want to create a user for LaraPrime, run `php artisan laraprime:admin`');
        $this->line('To start the embedded server run `php artisan serve`');
    }

    /**
     * Execute a command with parameters in silent mode.
     *
     * @return $this
     */
    private function executeCommand(string $command, array $parameters = []): self
    {
        try {
            $result = $this->callSilent($command, $parameters);
        } catch (\Exception $exception) {
            $result = 1;
            $this->alert($exception->getMessage());
        }

        if ($result) {
            $parameters = http_build_query($parameters, '', ' ');
            $parameters = str_replace('%5C', '/', $parameters);
            $this->alert("An error has occurred. The '{$command} {$parameters}' command was not executed");
        }

        return $this;
    }

    private function showMeLove(): self
    {
        if (App::runningUnitTests() || ! $this->confirm('Would you like to show a little love by starting with ⭐️ the LaraPrime repository?')) {
            return $this;
        }

        $repo = LaraPrime::repo();

        match (PHP_OS_FAMILY) {
            'Darwin' => exec("open $repo"),
            'Windows' => exec("start $repo"),
            'Linux' => exec("xdg-open $repo"),
            default => $this->line("You can find us at 📚: $repo")
        };

        $this->line('Thank you! It means a lot to us ❤️');

        return $this;
    }
}
