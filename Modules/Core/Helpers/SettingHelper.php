<?php

namespace Modules\Core\Helpers;

use Illuminate\Support\Facades\Cache;
use Modules\Core\External\Repositories\SettingRepository;

class SettingHelper
{
    protected SettingRepository $settingRepository;

    protected array $cache = [];

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function setting(string $key, $default = null)
    {
        $settings = Cache::rememberForever('settings', function () {
            return $this->settingRepository->all(null, [], ['mainImageRelation']);
        });

        return $settings->where('key', $key)->first();
    }

    public function currency(): string
    {
        return $this->setting('currency')?->value ?? 'rial';
    }

    public function currencyLabel(): string
    {
        return match ($this->currency()) {
            'toman' => 'تومان',
            default => 'ریال',
        };
    }
}
