<?php

namespace Modules\Core\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Core\Enums\SettingType;
use Modules\Core\External\Repositories\Contract\SettingRepositoryInterface;
use Modules\Media\Entities\Media;
use Modules\Media\Services\MediaService;

class SettingService
{
    public function __construct(
        protected SettingRepositoryInterface $settingRepository,
        protected MediaService $mediaService
    ) {}

    public function list(?string $orderBy = null, array $limit = [], array $with = [], array $conditions = [])
    {
        return $this->settingRepository->all($orderBy, $limit, $with, $conditions);
    }

    public function find($id)
    {
        return $this->settingRepository->find($id);
    }

    public function update(array $data)
    {
        return DB::transaction(function () use ($data) {
            foreach ($data as $key => $value) {
                if (isset($value)) {
                    $setting = $this->settingRepository->findByColumn('key', $key);
                    if ($setting->type == SettingType::IMAGE->value) {
                        $oldImage = $setting->main_image;
                        $dir = $setting->uploadDir();
                        $this->mediaService->upload($setting, $value, 'main', $dir);
                        if ($oldImage instanceof Media and get_class($oldImage) == Media::class) {
                            $this->mediaService->delete($oldImage);
                        }
                    } else {
                        $setting = $this->settingRepository->update($setting, ['value' => $value]);
                    }
                }
            }
            Cache::forget('settings');

            return $setting;
        });
    }

    public function deleteMedia(string $key)
    {
        $setting = $this->settingRepository->findByColumn('key', $key);
        $media = $setting?->medias?->first();

        if (! $media) {
            return false;
        }

        $this->mediaService->delete($media);
        Cache::forget('settings');

        return true;
    }
}
