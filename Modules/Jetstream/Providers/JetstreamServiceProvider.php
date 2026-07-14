<?php

namespace Modules\Jetstream\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;
use Livewire\Livewire;
use Modules\Core\Helpers\SettingHelper;
use Modules\Jetstream\Actions\RedirectAfterLogin;
use Modules\Jetstream\External\Repositories\Contract\OtpRepositoryInterface;
use Modules\Jetstream\External\Repositories\OtpRepository;
use Modules\Jetstream\Http\Livewire\CaptchaImage;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class JetstreamServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected string $name = 'Jetstream';

    protected string $nameLower = 'jetstream';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommands();
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'Database/migrations'));

        // $this->mergeConfigFrom(
        //     module_path($this->name, 'config/captcha.php'),
        //     'jetstreamcaptcha.captcha'
        // );

        // Register the lowercase "jetstream" view namespace for anonymous
        // components (<x-jetstream::layouts.master> etc.). This MUST run in
        // console too, otherwise `artisan view:cache` / `optimize` cannot
        // resolve the component and fails the deploy.
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'jetstream');

        // Component aliases and view-namespace prepends used when compiling
        // Blade (e.g. <x-guest-layout> on the auth pages). These MUST run in
        // console too, otherwise `artisan view:cache` / `optimize` cannot
        // resolve them and the deploy fails.
        Blade::component('jetstream::layouts.guest', 'guest-layout');
        View::prependNamespace('auth', base_path('Modules/Jetstream/resources/views/auth'));
        View::prependNamespace('components', base_path('Modules/Jetstream/resources/views/components'));
        Livewire::component('jetstream-captcha-image', CaptchaImage::class);

        if ($this->app->runningInConsole()) {
            return;
        }

        $settingHelper = app(SettingHelper::class);

        if ($settingHelper->setting('login_with_password')?->value) {
            Fortify::loginView(function () {
                return view('jetstream::auth.login');
            });
        } else {
            Fortify::loginView(function () {
                return view('jetstream::auth.login-without-password');
            });
        }

        Fortify::registerView(function () {
            return view('jetstream::auth.register');
        });

        Fortify::requestPasswordResetLinkView(function () {
            return view('jetstream::auth.forgot-password');
        });

        Fortify::resetPasswordView(function ($request) {
            return view('jetstream::auth.reset-password', ['request' => $request]);
        });
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        $this->app->singleton(LoginResponse::class, RedirectAfterLogin::class);
        $this->app['config']->set(
            'captcha',
            require __DIR__.'/../config/captcha.php'
        );

        $this->app->bind(OtpRepositoryInterface::class, OtpRepository::class);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        // $this->commands([]);
    }

    /**
     * Register command Schedules.
     */
    protected function registerCommandSchedules(): void
    {
        // $this->app->booted(function () {
        //     $schedule = $this->app->make(Schedule::class);
        //     $schedule->command('inspire')->hourly();
        // });
    }

    /**
     * Register translations.
     */
    public function registerTranslations(): void
    {
        $productLangPath = module_path($this->name, 'resources/lang');

        if (is_dir($productLangPath)) {
            $this->loadTranslationsFrom($productLangPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($productLangPath);
        }
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = module_path($this->name, config('modules.paths.generator.config.path'));

        if (is_dir($configPath)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($configPath));

            foreach ($iterator as $file) {
                if ($file->isFile() && $file->getExtension() === 'php') {
                    $config = str_replace($configPath.DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower.'.'.$config_key);

                    // Remove duplicated adjacent segments
                    $normalized = [];
                    foreach ($segments as $segment) {
                        if (end($normalized) !== $segment) {
                            $normalized[] = $segment;
                        }
                    }

                    $key = ($config === 'config.php') ? $this->nameLower : implode('.', $normalized);

                    $this->publishes([$file->getPathname() => config_path($config)], 'config');
                    $this->merge_config_from($file->getPathname(), $key);
                }
            }
        }
    }

    /**
     * Merge config from the given path recursively.
     */
    protected function merge_config_from(string $path, string $key): void
    {
        $existing = config($key, []);
        $module_config = require $path;

        config([$key => array_replace_recursive($existing, $module_config)]);
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/'.$this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower.'-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->name);

        Blade::componentNamespace(config('modules.namespace').'\\'.$this->name.'\\view\\components', $this->nameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach (config('view.paths') as $path) {
            if (is_dir($path.'/modules/'.$this->nameLower)) {
                $paths[] = $path.'/modules/'.$this->nameLower;
            }
        }

        return $paths;
    }
}
