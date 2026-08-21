<?php

namespace Modules\User\External\Repositories;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\External\Repositories\BaseRepository;
use Modules\Core\Helpers\SlugHelper;
use Modules\Tenant\Entities\Tenant;
use Modules\Tenant\Enums\TenantStatus;
use Modules\User\External\Repositories\Contract\TenantRepositoryInterface;

class TenantRepository extends BaseRepository implements TenantRepositoryInterface
{
    public function __construct(Tenant $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): Model
    {
        return Tenant::create([
            'name' => $data['name'],
            'slug' => SlugHelper::generate(get_class(new Tenant), $data['name'], 'slug'),
            'timezone' => $data['timezone'] ?? 'Asia/Tehran',
            'local' => $data['local'] ?? 'fa',
            'status' => $data['status'] ?? TenantStatus::ACTIVE->value,

        ]);
    }

    public function restore($code): Tenant
    {
        $tenant = Tenant::withTrashed()->where('slug', $code)->first();
        if ($tenant) {
            $tenant->restore();
        }

        return $tenant;
    }
}
