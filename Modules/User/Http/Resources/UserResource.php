<?php

namespace Modules\User\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\ACL\Http\Resources\PermissionResource;
use Modules\ACL\Http\Resources\RoleResource;
use Modules\Media\Http\Resources\MediaResource;
use Modules\Process\Http\Resources\ProcessResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'mobile' => $this->mobile,
            'slug' => $this->unique_code,
            'active' => $this->active,
            'expired_at' => $this->expired_at,
            'image' => new MediaResource($this->whenLoaded('mainImageRelation')),
            'roles' => RoleResource::collection(
                $this->whenLoaded('roles')
            ),
            'permissions' => PermissionResource::collection(
                $this->whenLoaded('permissions')
            ),
            'processes' => ProcessResource::collection(
                $this->whenLoaded('processes')
            ),
        ];
    }
}
