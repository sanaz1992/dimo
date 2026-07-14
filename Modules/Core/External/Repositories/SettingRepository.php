<?php

namespace Modules\Core\External\Repositories;

use Modules\Core\Entities\Setting;
use Modules\Core\External\Repositories\BaseRepository;
use Modules\Core\External\Repositories\Contract\SettingRepositoryInterface;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }
}
