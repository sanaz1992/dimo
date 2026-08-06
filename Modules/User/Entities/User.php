<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Core\Traits\Filterable;
use Modules\Media\Entities\Media;
use Modules\Media\Entities\NullMedia;
use Modules\Process\Entities\Process;
use Modules\User\Enums\UserLevel;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use Filterable;
    use HasApiTokens;
    use HasRoles;
    use Notifiable;
    use SoftDeletes;

    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'mobile',
        'password',
        'level',
        'unique_code',
        'active',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
            'level' => UserLevel::class,
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'unique_code';
    }

    public function uploadDir(): string
    {
        return 'uploads/users/'.$this->id;
    }

    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediaable');
    }

    public function getStatusAttribute()
    {
        if ($this->active) {
            return ['color' => 'bg-green-50 text-green-700 inset-ring-green-600/20', 'title' => 'فعال'];
        } else {
            return ['color' => 'bg-red-50 text-red-700 inset-ring-red-600/10', 'title' => 'غیرفعال'];
        }
    }

    public function getCreatedAtJalaliDateAttribute()
    {
        return verta($this->created_at)->format('Y/m/d');
    }

    public function getUpdatedAtJalaliDateAttribute()
    {
        return verta($this->updated_at)->format('Y/m/d');
    }

    public function getExpiredAtJalaliDateAttribute()
    {
        return verta($this->expired_at)->format('Y/m/d H:i:s');
    }

    public function mainImageRelation(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediaable')
            ->where('collection', 'avatar');
    }

    public function getAvatarAttribute()
    {
        if ($this->relationLoaded('mainImageRelation')) {
            return $this->getRelation('mainImageRelation') ?? new NullMedia;
        }

        return $this->mainImageRelation()->first() ?? new NullMedia;
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function processes(): BelongsToMany
    {
        return $this->belongsToMany(
            Process::class,
            'process_assignments'
        );
    }

    // public function sellerOrders(): HasMany
    // {
    //     return $this->hasMany(Order::class, 'seller_id');
    // }

    // public function userOrders()
    // {
    //     return $this->hasMany(Order::class, 'user_id');
    // }

    // public function charts(): BelongsToMany
    // {
    //     return $this->belongsToMany(Chart::class, 'user_chart');
    // }
    // public function hasChart($chart): bool
    // {
    //     if (is_numeric($chart)) {
    //         return $this->charts->contains('id', $chart);
    //     }

    //     return $this->charts->contains('name', $chart);
    // }
}
