<?php

namespace Modules\Product\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Product\Console\CleanDuplicateCostItemsCommand;
use Modules\Product\External\Contracts\ProductRepositoryInterface;
use Modules\Product\External\Contracts\ProductSkuRepositoryInterface;
use Modules\Product\External\ProductRepository;
use Modules\Product\External\ProductSkuRepository;
use Modules\Product\Http\Livewire\Admin\Product\ProductCreate;
use Modules\Product\Http\Livewire\Admin\Product\ProductEdit;
use Modules\Product\Http\Livewire\Admin\Product\ProductImport;
use Modules\Product\Http\Livewire\Admin\Product\ProductList;
use Modules\Product\Http\Livewire\Component\ProductAdvancedFilters;
use Modules\Product\Services\ProductPriceStrategy\FixedPriceStrategy;
use Modules\Product\Services\ProductPriceStrategy\ProductPriceResolver;
use Modules\Product\Services\ProductPriceStrategy\SmartCostStrategy;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ProductServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected $defer = true;

    protected string $name = 'Product';

    protected string $nameLower = 'product';

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

        Livewire::component('product::create', ProductCreate::class);
        Livewire::component('product::edit', ProductEdit::class);
        Livewire::component('product::list', ProductList::class);
        Livewire::component('product::import', ProductImport::class);

        Livewire::component('product::product-advanced-filters', ProductAdvancedFilters::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductSkuRepositoryInterface::class, ProductSkuRepository::class);

        $this->app->singleton(ProductPriceResolver::class, function ($app) {
            return new ProductPriceResolver([
                $app->make(SmartCostStrategy::class),
                $app->make(FixedPriceStrategy::class),
            ]);
        });

        $this->commands([
            CleanDuplicateCostItemsCommand::class,
        ]);
    }

    /**
     * Register commands in the format of Command::class
     */
    protected function registerCommands(): void
    {
        $this->commands([
            CleanDuplicateCostItemsCommand::class,
        ]);
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

        Blade::componentNamespace(config('modules.namespace').'\\'.$this->name.'\\View\\Components', $this->nameLower);
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
