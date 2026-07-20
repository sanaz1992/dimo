<?php

namespace Modules\User\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\ACL\Services\UserRoleService;
use Modules\Core\Http\Controllers\Api\ApiResponseTrait;
use Modules\Media\Http\Resources\MediaResource;
use Modules\User\Entities\User;
use Modules\User\Enums\UserLevel;
use Modules\User\Filters\UserFilter;
use Modules\User\Http\Resources\UserResource;
use Modules\User\Requests\StoreUserRequests;
use Modules\User\Requests\UpdateUserImageRequest;
use Modules\User\Requests\UpdateUserRequests;
use Modules\User\Requests\UpdateUserRoleRequests;
use Modules\User\Services\UserService;

class AdminController extends Controller
{
    use ApiResponseTrait;

    public function __construct(protected UserService $userService)
    {
        $this->userService = $userService;

        $this->middleware('can:admins_list')->only(['index']);
        $this->middleware('can:admins_create')->only(['store']);
        $this->middleware('can:admins_roles_edit')->only(['getAdminRoles']);
    }

    public function index(UserFilter $filter)
    {

        $level = $filter->get('level');

        if ($level) {
            if (! UserLevel::tryFrom($level)) {
                return $this->respondError(
                    'مقدار درخواستی برای level معتبر نیست',
                    422
                );
            }
        } else {
            $level = UserLevel::ADMIN->value;
        }

        try {
            $conditions = [
                'where' => [
                    'level' => ['=', $level],
                    // 'id' => ['!=', 1]
                ],
            ];
            $deleted = $filter->get('deleted');
            if ($deleted) {
                $conditions = array_merge($conditions, [
                    'trashed' => 'only',
                ]);
            }
            // get admin lists
            $users = $this->userService->list(null, [10, true], ['mainImageRelation'], $conditions, $filter);

            return $this->respondSuccess(
                UserResource::collection($users)->resolve()
            );
        } catch (\Exception $e) {
            Log::error('Error fetching admins: '.$e->getMessage());

            return $this->respondError('خطا در بازیابی لیست مدیران.', 500); // 500 Internal Server Error
        }
    }

    public function show(User $admin)
    {
        $admin->load('roles', 'permissions');

        return $this->respondSuccess(
            (new UserResource($admin))->resolve()
        );
    }

    public function store(StoreUserRequests $request)
    {
        $data = $request->all();
        $data['level'] = $data['level'] ?? UserLevel::ADMIN->value;
        $user = $this->userService->create($data);
        $user->load('roles', 'permissions');

        return $this->respondSuccess(
            (new UserResource($user))->resolve()
        );
    }

    public function update(User $admin, UpdateUserRequests $request)
    {
        $data = $request->all();
        // $data['level'] = $data['level'] ?? UserLevel::ADMIN->value;
        $user = $this->userService->update($admin, $data);
        $user->load('roles', 'permissions');

        return $this->respondSuccess(
            (new UserResource($user))->resolve()
        );
    }

    public function destroy(User $admin)
    {
        $this->userService->delete($admin);

        return $this->respondSuccess(
            'کاربر با موفقیت حذف شد.'
        );
    }

    public function restore($user)
    {
        $user = $this->userService->restore($user);

        return $this->respondSuccess(
            (new UserResource($user))->resolve(),
            'کاربر با موفقیت فعال شد.'
        );
    }

    public function getAdminRoles(User $user)
    {
        $user->load('roles', 'permissions');

        return $this->respondSuccess(
            (new UserResource($user))->resolve()
        );
    }

    public function updateAdminRoles(User $user, UpdateUserRoleRequests $request)
    {
        $data = $request->all();
        $data['selectedRoles'] = $data['roles'];
        $data['selectedPermissions'] = $data['permissions'];
        resolve(UserRoleService::class)->updateUserRoles($user, $data);
        $user->load('roles', 'permissions');

        return $this->respondSuccess(
            (new UserResource($user))->resolve()
        );
    }

    public function getAdminLevels()
    {
        return $this->respondSuccess(
            UserLevel::adminLabels(),
            'عملیات با موفقیت انجام شد.',
            201
        );
    }

    public function getAllAdminPermissions(User $user)
    {
        $permissions = $user->getAllPermissions();
        $grouped = collect($permissions)
            ->map(fn ($item) => collect($item)->only(['name', 'title', 'section', 'group']))
            ->groupBy(['section', 'group']);

        return $this->respondSuccess(
            $grouped,
            'عملیات با موفقیت انجام شد.',
            201
        );
    }

    public function updateUserImage(UpdateUserImageRequest $request, User $user): JsonResponse
    {
        $media = $this->userService->updateImage($user, $request->file('image'));

        return $this->respondSuccess(
            data: new MediaResource($media)
        );
    }
}
