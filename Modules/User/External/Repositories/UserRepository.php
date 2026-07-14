<?php

namespace Modules\User\External\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Modules\Core\External\Repositories\BaseRepository;
use Modules\Core\Helpers\CodeGeneratorHelper;
use Modules\Core\Helpers\ConvertDatesHelper;
use Modules\User\Entities\User;
use Modules\User\Enums\UserLevel;
use Modules\User\External\Repositories\Contract\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): Model
    {
        return User::create([
            'name'     => $data['name'],
            'mobile'   => $data['mobile'],
            'password' => Hash::make(ConvertDatesHelper::convertPersianNumbersToEnglish($data['password'])),
            'level'    => $data['level'] ?? UserLevel::USER->value,
            'unique_code' => CodeGeneratorHelper::generate(get_class(new User()), 'unique_code'),
            'active' => $data['active'] ?? $data['is_active'] ?? false,
            'expired_at' => $data['expired_at'] ?? null
        ]);
    }

    public function update(Model $user, array $data): ?Model
    {
        $user->name = $data['name'];
        $user->mobile = $data['mobile'];
        if (isset($data['password']) && strlen($data['password'])) {
            $user->password = Hash::make(ConvertDatesHelper::convertPersianNumbersToEnglish($data['password']));
        }
        $user->active = $data['active'] ?? $data['is_active'] ?? false;
        $user->save();
        return $user;
    }

    public function updateCharts(User $user, array $chartsId): void
    {
        $user->charts()->sync($chartsId);
    }

    public function restore($code): User
    {
        $user = User::withTrashed()->where('unique_code', $code)->first();
        if ($user) {
            $user->restore();
        }

        return $user;
    }
}
