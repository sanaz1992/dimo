<?php

namespace Modules\User\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Modules\ACL\Services\UserRoleService;
use Modules\Core\Exceptions\ApiException;
use Modules\Core\Filters\QueryFilter;
use Modules\Core\Helpers\CodeGeneratorHelper;
use Modules\Media\Entities\Media;
use Modules\Media\Services\MediaService;
use Modules\Process\Enums\ProcessType;
use Modules\Process\Services\ProcessService;
use Modules\User\Entities\User;
use Modules\User\Enums\UserLevel;
use Modules\User\External\Repositories\Contract\AddressRepositoryInterface;
use Modules\User\External\Repositories\Contract\UserRepositoryInterface;

class UserService
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected MediaService $mediaService,
        protected AddressRepositoryInterface $addressRepository,
    ) {}

    public function list(string $orderBy = null, array $limit = [], array $with = [], array $conditions = [], QueryFilter $filter = null)
    {
        return $this->userRepository->all($orderBy, $limit, $with, $conditions, $filter);
    }

    public function findByColumn($col, $value)
    {
        return $this->userRepository->findByColumn($col, $value);
    }

    public function firstOrCreate($condition, $data)
    {
        if (!isset($data['unique_code'])) {
            $data['unique_code'] = CodeGeneratorHelper::generate(get_class(new User()), 'unique_code');
        }
        return $this->userRepository->firstOrCreate(
            $condition,
            $data
        );
    }

    public function findOrCreateUser(array $data, string $col): User
    {

        $user = resolve(UserService::class)->findByColumn($col, $data[$col]);
        if (!$user) {
            $user = resolve(UserService::class)->create([
                'name' => $data['name'],
                'mobile' => $data['mobile'],
                'password' => $data['password'],
            ]);
        }
        return $user;
    }

    public function create(array $data): User
    {
        $image = $data['image'] ?? null;
        unset($data['image']);
        $user = $this->userRepository->findByColumn('mobile', $data['mobile'], true);
        if ($user && $user->trashed()) {
            throw new ApiException(
                'دسترسی این یوزر غیرفعال شده است. لطفا برای فعال کردن دسترسی با مدیریت هماهنگ کنید.',
                500
            );
        }
        return DB::transaction(function () use ($data, $image) {
            $user = $this->userRepository->create($data);
            if (isset($data['city_id'])) {
                $data['user_id'] = $user->id;
                $data['type'] = 'user-address';
                $this->addressRepository->create($data);
            }
            if ((isset($data['roles']) && $data['roles']) || (isset($data['permissions']) && $data['permissions'])) {
                $data['selectedRoles'] = $data['roles'];
                $data['selectedPermissions'] = $data['permissions'];
                resolve(UserRoleService::class)->updateUserRoles($user, $data);
            }
            if (isset($data['level']) && $data['level'] == UserLevel::SALES_OPERATOR->value) {
                resolve(UserRoleService::class)->updateUserRoles(
                    $user,
                    ['selectedRoles' => 'sales_operator']
                );
            }

            if ($image) {
                $dir = $user->uploadDir();
                $this->mediaService->upload($user, $image, 'avatar', $dir);
            }
            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        $image = $data['image'] ?? null;
        unset($data['image']);
        return DB::transaction(function () use ($user, $data, $image) {
            $user = $this->userRepository->update($user, $data);
            if (isset($data['city_id'])) {
                $address = $user->addresses()->first();
                $this->addressRepository->update($address, $data);
            }
            if ((isset($data['roles']) && $data['roles']) || (isset($data['permissions']) && $data['permissions'])) {
                $data['selectedRoles'] = $data['roles'] ?? [];
                $data['selectedPermissions'] = $data['permissions'] ?? [];
                resolve(UserRoleService::class)->updateUserRoles($user, $data);
            }

            if ($image) {
                $oldImage = $user->medias()->first();
                $dir = $user->uploadDir();
                $this->mediaService->upload($user, $image, 'avatar', $dir);
                if ($oldImage instanceof Media) {
                    $this->mediaService->delete($oldImage);
                }
            }
            return $user;
        });
    }

    public function delete(User $user): bool
    {
        return $this->userRepository->delete($user->id);
    }

    public function restore($userCode)
    {
        return $this->userRepository->restore($userCode);
    }

    public function updateImage(User $user, UploadedFile $image)
    {
        return DB::transaction(function () use ($user, $image) {
            $oldImage = $user->medias()->first();
            $dir = $user->uploadDir();
            $newImage = $this->mediaService->upload($user, $image, 'avatar', $dir);
            if ($oldImage instanceof Media) {
                $this->mediaService->delete($oldImage);
            }
            return $newImage;
        });
    }
}
