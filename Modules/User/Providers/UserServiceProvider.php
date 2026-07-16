<?php

namespace Modules\User\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\User\External\Repositories\AddressRepository;
use Modules\User\External\Repositories\CityRepository;
use Modules\User\External\Repositories\Contract\AddressRepositoryInterface;
use Modules\User\External\Repositories\Contract\CityRepositoryInterface;
use Modules\User\External\Repositories\Contract\ProvinceRepositoryInterface;
use Modules\User\External\Repositories\Contract\UserRepositoryInterface;
use Modules\User\External\Repositories\ProvinceRepository;
use Modules\User\External\Repositories\UserRepository;
use Modules\User\Http\Livewire\Admin\User\UserCreate;
use Modules\User\Http\Livewire\Admin\User\UserEdit;
use Modules\User\Http\Livewire\Admin\User\UserList;
// use Modules\User\Http\Livewire\Admin\Admin\AdminCreate;
// use Modules\User\Http\Livewire\Admin\Admin\AdminEdit;
// use Modules\User\Http\Livewire\Admin\Admin\AdminList;
// use Modules\User\Http\Livewire\Admin\Seller\SellerCreate;
// use Modules\User\Http\Livewire\Admin\Seller\SellerEdit;
// use Modules\User\Http\Livewire\Admin\Seller\SellerList;
// use Modules\User\Http\Livewire\Admin\User\UserCreate;
// use Modules\User\Http\Livewire\Admin\User\UserEdit;
// use Modules\User\Http\Livewire\Admin\User\UserList;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class UserServiceProvider extends ServiceProvider
{
    use PathNamespace;
    protected $defer = true;


    protected string $name = 'User';

    protected string $nameLower = 'user';

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

        // Livewire::component('admin::list', AdminList::class);
        // Livewire::component('admin::create', AdminCreate::class);
        // Livewire::component('admin::edit', AdminEdit::class);

        // Livewire::component('seller::list', SellerList::class);
        // Livewire::component('seller::create', SellerCreate::class);
        // Livewire::component('seller::edit', SellerEdit::class);

        Livewire::component('user::list', UserList::class);
        Livewire::component('user::create', UserCreate::class);
        Livewire::component('user::edit', UserEdit::class);

    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AddressRepositoryInterface::class, AddressRepository::class);
        $this->app->bind(ProvinceRepositoryInterface::class, ProvinceRepository::class);
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
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
        $LangPath = module_path($this->name, 'resources/lang');

        if (is_dir($LangPath)) {
            $this->loadTranslationsFrom($LangPath, $this->nameLower);
            $this->loadJsonTranslationsFrom($LangPath);
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
                    $config = str_replace($configPath . DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $config_key = str_replace([DIRECTORY_SEPARATOR, '.php'], ['.', ''], $config);
                    $segments = explode('.', $this->nameLower . '.' . $config_key);

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
        $viewPath = resource_path('views/modules/' . $this->nameLower);
        $sourcePath = module_path($this->name, 'resources/views');

        $this->publishes([$sourcePath => $viewPath], ['views', $this->nameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->name);

        Blade::componentNamespace(config('modules.namespace') . '\\' . $this->name . '\\View\\Components', $this->nameLower);
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
            if (is_dir($path . '/modules/' . $this->nameLower)) {
                $paths[] = $path . '/modules/' . $this->nameLower;
            }
        }

        return $paths;
    }
}
