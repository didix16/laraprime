<?php

namespace Didix16\LaraPrime;

use Closure;
use Didix16\LaraPrime\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Throwable;

class LaraPrime
{
    use Concerns\AuthorizesRequests;
    use Concerns\HandlesRoutes;
    use Concerns\InteractsWithEvents;

    /**
     * All available themes from primereact
     *
     * @var array<string, mixed>
     */
    public static array $themes = [
        'arya' => [
            'name' => 'Arya',
            'hasDark' => false,
            'hasFonts' => false,
            'themes' => [
                'blue',
                'green',
                'orange',
                'purple',
            ],
        ],

        'bootstrap4' => [
            'name' => 'Bootstrap 4',
            'hasDark' => true,
            'hasFonts' => false,
            'themes' => [
                'blue',
                'purple',
            ],
        ],
        'fluent-light' => [
            'name' => 'Fluent Light',
            'hasDark' => false,
            'hasFonts' => false,
        ],
        'lara' => [
            'name' => 'Lara',
            'hasDark' => true,
            'hasFonts' => true,
            'themes' => [
                'amber',
                'blue',
                'cyan',
                'green',
                'indigo',
                'pink',
                'purple',
                'teal',
            ],
        ],
        'luna' => [
            'name' => 'Luna',
            'hasDark' => false,
            'hasFonts' => false,
            'themes' => [
                'amber',
                'blue',
                'green',
                'pink',
            ],
        ],
        'md' => [
            'name' => 'Material Design',
            'hasDark' => true,
            'hasFonts' => true,
            'themes' => [
                'deeppurple',
                'indigo',
            ],
        ],
        'mdc' => [
            'name' => 'Material Design Compact',
            'hasDark' => true,
            'hasFonts' => true,
            'themes' => [
                'deeppurple',
                'indigo',
            ],
        ],
        'mira' => [
            'name' => 'Mira',
            'hasDark' => false,
            'hasFonts' => true,
        ],
        'nova' => [
            'name' => 'Nova',
            'hasDark' => false,
            'hasFonts' => false,
        ],
        'nova-accent' => [
            'name' => 'Nova Accent',
            'hasDark' => false,
            'hasFonts' => false,
        ],
        'nova-alt' => [
            'name' => 'Nova Alt',
            'hasDark' => false,
            'hasFonts' => false,
        ],
        'rhea' => [
            'name' => 'Rhea',
            'hasDark' => false,
            'hasFonts' => false,
        ],
        'saga' => [
            'name' => 'Saga',
            'hasDark' => false,
            'hasFonts' => false,
            'themes' => [
                'blue',
                'green',
                'orange',
                'purple',
            ],
        ],
        'soho' => [
            'name' => 'Soho',
            'hasDark' => true,
            'hasFonts' => true,
        ],
        'tailwind-light' => [
            'name' => 'Tailwind Light',
            'hasDark' => false,
            'hasFonts' => true,
        ],
        'vela' => [
            'name' => 'Vela',
            'hasDark' => false,
            'hasFonts' => false,
            'themes' => [
                'blue',
                'green',
                'orange',
                'purple',
            ],
        ],
        'viva' => [
            'name' => 'Viva',
            'hasDark' => true,
            'hasFonts' => true,
        ],
    ];

    /**
     * The variables that should be made available on the LaraPrime JavaScript object.
     *
     * @var array<string, mixed>
     */
    public static array $jsonVariables = [];

    /**
     * The initial path LaraPrime should route to when visiting the base.
     */
    public static string $initialPath = '/main';

    /**
     * Indicates if LaraPrime is being used to authenticate users.
     */
    public static bool $withAuthentication = false;

    /**
     * The callback used to resolve LaraPrime's footer.
     *
     * @var (Closure(Request):(string))|null
     */
    public static $footerCallback;

    final public function __construct() {}

    /**
     * Get the specified theme data.
     */
    public static function theme(string $themeFamily): ?array
    {
        return self::$themes[$themeFamily] ?? null;
    }

    /**
     * Get the default theme for LaraPrime.
     */
    public static function defaultTheme(): string
    {
        return config('laraprime.theme', 'mira');
    }

    /**
     * Get the public path to a theme.
     */
    public static function publicThemePath($themeSlug): string
    {
        return public_path(sprintf('vendor/laraprime/themes/%s', $themeSlug));
    }

    /**
     * List all available themes.
     * If $installed is true, only installed themes will be listed.
     *
     * @return Collection<string, array>
     */
    public static function listThemes(bool $installed = false): Collection
    {
        return $installed ?
            collect(self::$themes)->mapWithKeys(function ($theme, $themeFamily) {
                $hasDark = $theme['hasDark'] ?? false;
                $hasThemes = isset($theme['themes']) && is_array($theme['themes']);

                $themes = [];

                $themeFamilyTitle = Str::title($themeFamily);

                if ($hasDark && $hasThemes) {
                    foreach ($theme['themes'] as $subTheme) {
                        $themeName = $themeFamily . '-dark-' . $subTheme;
                        if (File::exists(static::publicThemePath($themeName) . '/theme.css')) {
                            $themes[$themeName] = [
                                'family' => $themeFamilyTitle,
                                'name' => Str::title(Str::replace('-', ' ', $themeFamily . '-' . $subTheme)),
                                'subTheme' => $subTheme,
                                'hasVariance' => false,
                            ];
                        }
                        $themeName = $themeFamily . '-light-' . $subTheme;
                        if (File::exists(static::publicThemePath($themeName) . '/theme.css')) {
                            $themes[$themeName] = [
                                'family' => $themeFamilyTitle,
                                'name' => Str::title(Str::replace('-', ' ', $themeFamily . '-' . $subTheme)),
                                'subTheme' => $subTheme,
                                'hasVariance' => false,
                            ];
                        }
                    }
                } elseif ($hasThemes) {
                    foreach ($theme['themes'] as $subTheme) {
                        $themeName = $themeFamily . '-' . $subTheme;
                        if (File::exists(static::publicThemePath($themeName) . '/theme.css')) {
                            $themes[$themeName] = [
                                'family' => $themeFamilyTitle,
                                'name' => Str::title(Str::replace('-', ' ', $themeFamily . '-' . $subTheme)),
                                'subTheme' => $subTheme,
                                'hasVariance' => false,
                            ];
                        }
                    }
                } elseif ($hasDark) {
                    $themeName = $themeFamily . '-dark';
                    if (File::exists(static::publicThemePath($themeName) . '/theme.css')) {
                        $themes[$themeName] = [
                            'family' => $themeFamilyTitle,
                            'name' => Str::title(Str::replace('-', ' ', $themeFamily . '-dark')),
                            'subTheme' => null,
                            'hasVariance' => true,
                        ];
                    }
                    $themeName = $themeFamily . '-light';
                    if (File::exists(static::publicThemePath($themeName) . '/theme.css')) {
                        $themes[$themeName] = [
                            'family' => $themeFamilyTitle,
                            'name' => Str::title(Str::replace('-', ' ', $themeFamily . '-light')),
                            'subTheme' => null,
                            'hasVariance' => true,
                        ];
                    }
                } else {
                    $themeName = $themeFamily;
                    if (File::exists(static::publicThemePath($themeName) . '/theme.css')) {
                        $themes[$themeFamily] = [
                            'family' => $themeFamilyTitle,
                            'name' => Str::title(Str::replace('-', ' ', $themeFamily)),
                            'subTheme' => null,
                            'hasVariance' => false,
                        ];
                    }
                }

                return $themes;
            }) :
            collect(self::$themes)->mapWithKeys(function ($theme, $themeFamily) {
                $hasDark = $theme['hasDark'] ?? false;
                $hasThemes = isset($theme['themes']) && is_array($theme['themes']);

                $themes = [];
                $themeFamilyTitle = Str::title($themeFamily);
                if ($hasDark && $hasThemes) {
                    foreach ($theme['themes'] as $subTheme) {
                        $themes[$themeFamily . '-dark-' . $subTheme] = [
                            'family' => $themeFamilyTitle,
                            'name' => Str::title(Str::replace('-', ' ', $themeFamily . '-dark-' . $subTheme)),
                            'subTheme' => $subTheme,
                            'hasVariance' => true,
                        ];
                        $themes[$themeFamily . '-light-' . $subTheme] = [
                            'family' => $themeFamilyTitle,
                            'name' => Str::title(Str::replace('-', ' ', $themeFamily . '-light-' . $subTheme)),
                            'subTheme' => $subTheme,
                            'hasVariance' => true,
                        ];
                    }
                } elseif ($hasThemes) {
                    foreach ($theme['themes'] as $subTheme) {
                        $themes[$themeFamily . '-' . $subTheme] = [
                            'family' => $themeFamilyTitle,
                            'name' => Str::title(Str::replace('-', ' ', $themeFamily . '-' . $subTheme)),
                            'subTheme' => $subTheme,
                            'hasVariance' => false,
                        ];
                    }
                } elseif ($hasDark) {
                    $themes[$themeFamily . '-dark'] = [
                        'family' => $themeFamilyTitle,
                        'name' => Str::title(Str::replace('-', ' ', $themeFamily . '-dark')),
                        'subTheme' => null,
                        'hasVariance' => true,
                    ];
                    $themes[$themeFamily . '-light'] = [
                        'family' => $themeFamilyTitle,
                        'name' => Str::title(Str::replace('-', ' ', $themeFamily . '-light')),
                        'subTheme' => null,
                        'hasVariance' => true,
                    ];
                } else {
                    $themes[$themeFamily] = [
                        'family' => $themeFamilyTitle,
                        'name' => Str::title(Str::replace('-', ' ', $themeFamily)),
                        'subTheme' => null,
                        'hasVariance' => false,
                    ];
                }

                return $themes;
            });
    }

    /**
     * Read a meta key from the LaraPrime composer manifest.
     */
    private static function readMetaKeyFromComposerManifest(string $metaKey): ?string
    {
        $manifest = json_decode(File::get(self::packagePath('composer.json')), true);

        return $manifest[$metaKey] ?? null;
    }

    /**
     * Get the LaraPrime repository URL.
     */
    public static function repo(): string
    {
        return self::readMetaKeyFromComposerManifest('homepage') ?? 'https://github.com/didix16/laraprime';
    }

    /**
     * Get the current LaraPrime version.
     */
    public static function version(): string
    {
        return Cache::driver('array')->rememberForever('laraprime.version', function () {

            $version = self::readMetaKeyFromComposerManifest('version') ?? '1.x';

            return $version . ' (Optimus Prime)';
        });
    }

    /**
     * Get the app name utilized by LaraPrime.
     */
    public static function name(): string
    {
        return config('laraprime.name', 'LaraPrime Admin Panel');
    }

    /**
     * Get the URI path prefix utilized by Nova.
     */
    public static function path(): string
    {
        return Str::start(config('laraprime.path', '/admin'), '/');
    }

    /**
     * The real path to the package files.
     */
    public static function packagePath($path = ''): string
    {
        $current = dirname(__DIR__);

        return realpath($current . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }

    /**
     * Register the LaraPrime routes.
     */
    public static function routes(): PendingRouteRegistation
    {
        Route::aliasMiddleware('laraprime.guest', RedirectIfAuthenticated::class);

        return new PendingRouteRegistation;
    }

    /**
     * Enable LaraPrime's authentication functionality.
     */
    public static function withAuthentication(): static
    {
        static::$withAuthentication = true;

        return new static;
    }

    /**
     * Get the JSON variables that should be provided to the global Nova JavaScript object.
     *
     * @return array<string, mixed>
     */
    public static function jsonVariables(Request $request): array
    {
        return collect(static::$jsonVariables)->map(function ($variable) use ($request) {
            return is_object($variable) && is_callable($variable)
                ? $variable($request)
                : $variable;
        })->all();
    }

    /**
     * Set the initial route path when visiting the base LaraPrime url.
     */
    public static function initialPath(string $path): static
    {
        static::$initialPath = $path;

        return new static;
    }

    /**
     * Provide additional variables to the global LaraPrime JavaScript object.
     *
     * @param  array<string, mixed>  $variables
     */
    public static function provideToScript(array $variables): static
    {
        if (empty(static::$jsonVariables)) {
            $userId = Auth::guard(config('laraprime.guard'))->id() ?? null;

            static::$jsonVariables = [
                'withAuthentication' => static::$withAuthentication,
                'customLoginPath' => config('laraprime.routes.login', false),
                'customLogoutPath' => config('laraprime.routes.logout', false),
                'initialPath' => static::$initialPath,
                'base' => static::path(),
                'userId' => $userId,
                'footer' => function ($request) {
                    return self::resolveFooter($request);
                },
            ];
        }

        static::$jsonVariables = array_merge(static::$jsonVariables, $variables);

        return new static;
    }

    /**
     * Resolve the footer used for LaraPrime.
     */
    public static function resolveFooter(Request $request): string
    {
        if (! is_null(static::$footerCallback)) {
            return call_user_func(static::$footerCallback, $request);
        }

        return static::defaultFooter($request);
    }

    /**
     * Resolve the default footer text used for Nova.
     */
    public static function defaultFooter(Request $request): string
    {
        return Blade::render('
            <p class="text-center">Powered by <a class="link-default" href="https://github.com/didix16/laraprime">LaraPrime</a> · v{!! $version !!}</p>
            <p class="text-center">&copy; {!! $year !!} didix16 &middot; by Dídac Rodríguez.</p>
        ', [
            'version' => static::version(),
            'year' => date('Y'),
        ]);
    }

    /**
     * Determine if the published LaraPrime assets are up to date.
     *
     * @throws Throwable
     */
    public static function assetsAreCurrent(): bool
    {
        $publishedPath = public_path('vendor/laraprime/manifest.json');

        throw_unless(File::exists($publishedPath), new \RuntimeException('LaraPrime assets are not published. Please run: `php artisan laraprime:publish`'));

        return File::get($publishedPath) === File::get(static::packagePath('build/manifest.json'));
    }
}
