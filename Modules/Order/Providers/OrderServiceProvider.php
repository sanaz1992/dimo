<?php

namespace Modules\Order\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Modules\Core\Observers\HistoryObserver;
use Modules\Order\Console\ImportManualOrdersCommand;
use Modules\Order\Entities\Order;
use Modules\Order\Entities\OrderItem;
use Modules\Order\External\Contracts\OrderItemRepositoryInterface;
use Modules\Order\External\Contracts\OrderRepositoryInterface;
use Modules\Order\External\OrderItemRepository;
use Modules\Order\External\OrderRepository;
use Modules\Order\Http\Livewire\Admin\OrderCreate;
use Modules\Order\Http\Livewire\Admin\OrderEdit;
use Modules\Order\Http\Livewire\Admin\OrderImport;
use Modules\Order\Http\Livewire\Admin\OrderList;
use Modules\Order\Http\Livewire\Admin\OrderShow;
use Modules\Order\Http\Livewire\Admin\OrderTrackingShow;
use Modules\Order\Http\Livewire\Component\AdvancedFilters;
use Modules\Order\Http\Livewire\ProductSelector;
use Modules\Order\Http\Livewire\ProductsTable;
use Modules\Order\Observers\OrderItemObserver;
use Nwidart\Modules\Traits\PathNamespace;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class OrderServiceProvider extends ServiceProvider
{
    use PathNamespace;

    protected $defer = true;

    protected string $name = 'Order';

    protected string $nameLower = 'order';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerCommandSchedules();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->name, 'Database/migrations'));

        Livewire::component('order::admin-create', OrderCreate::class);
        Livewire::component('order::admin-edit', OrderEdit::class);
        Livewire::component('order::admin-list', OrderList::class);
        Livewire::component('order::admin-import', OrderImport::class);
        Livewire::component('order::admin-show', OrderShow::class);
        Livewire::component('order::admin-order-tracking', OrderTrackingShow::class);

        Livewire::component('order::product-selector', ProductSelector::class);
        Livewire::component('order::products-table', ProductsTable::class);

        Livewire::component('order::advanced-filters', AdvancedFilters::class);

        Order::observe(HistoryObserver::class);
        OrderItem::observe(OrderItemObserver::class);
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(RouteServiceProvider::class);

        $this->app->bind(OrderRepositoryInterface::class, OrderRepository::class);
        $this->app->bind(OrderItemRepositoryInterface::class, OrderItemRepository::class);

        $this->commands([
            ImportManualOrdersCommand::class,
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
