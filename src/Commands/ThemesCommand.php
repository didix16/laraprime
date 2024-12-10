<?php

namespace Didix16\LaraPrime\Commands;

use Didix16\LaraPrime\LaraPrime;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

#[AsCommand(name: 'laraprime:themes')]
class ThemesCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'laraprime:themes {--installed : List only installed themes} {--install-only=* : Install only the specified themes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List and install all available LaraPrime themes';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $showInstalled = $this->option('installed');
        $installOnly = $this->option('install-only');
        $themes = LaraPrime::listThemes($showInstalled);

        // First list themes
        if ($showInstalled) {
            $this->info('Installed Themes:');
            if ($themes->isEmpty()) {
                $this->warn('⚠️ No themes installed');
                $this->comment('Did you forget to run `php artisan laraprime:install` or `php artisan laraprime:publish` ?');
                if ($this->confirm('Do you want to run `php artisan laraprime:publish` now?')) {
                    $this->call('laraprime:publish');
                    $this->info('LaraPrime default theme published successfully');
                    $themes = LaraPrime::listThemes($showInstalled);
                } else {
                    $this->comment('You can install themes by running `php artisan laraprime:themes --install-only=theme1-slug,theme2-slug`');
                    $this->comment('Run `php artisan laraprime:themes` to list available themes');

                    return;
                }
            }
        } else {
            $this->info('Available Themes:');
        }
        $this->table(['Theme slug', 'Theme Family', 'SubTheme', 'Dark/Light Variance'], $themes->map(fn ($theme, $themeSlug) => [
            $themeSlug,
            $theme['family'],
            $theme['subTheme'],
            $theme['hasVariance'] ? '✅' : '❌',
        ]
        )->all());

        if ($this->confirm('Do you want to install themes now?')) {
            $selectedThemes = $this->choice('Select themes to install', $themes = LaraPrime::listThemes()->keys()->toArray(), null, 1, true);
            $selectedThemes = $installOnly ? array_intersect($installOnly, $selectedThemes) : $selectedThemes;
            if (empty($selectedThemes)) {
                $this->warn('⚠️ No themes selected');

                return;
            }
            $this->info('Installing themes...');

            // use filesystem to copy theme files
            $filesystem = new Filesystem;
            foreach ($selectedThemes as $themeSlug) {
                $this->output->write("Installing theme: $themeSlug... ");

                if ($filesystem->copyDirectory(LaraPrime::packagePath("resources/themes/$themeSlug"), LaraPrime::publicThemePath($themeSlug))) {
                    $this->info("\033[1mOK\033[0m");
                } else {
                    $this->error("\033[1mFAIL\033[0m");
                }
            }
        }
    }
}
