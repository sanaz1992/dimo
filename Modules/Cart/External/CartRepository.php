<?php

namespace Modules\Cart\External;

use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Cart\Entities\Cart;
use Modules\Cart\External\Contracts\CartRepositoryInterface;
use Modules\Core\External\Repositories\BaseRepository;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    public function __construct(Cart $model)
    {
        parent::__construct($model);
    }

    public function getOrCreateActiveForUser(Authenticatable $user): Cart
    {
        return Cart::query()->firstOrCreate([
            'user_id' => $user->getAuthIdentifier(),
            'status' => 'active',
        ]);
    }

    public function getOrCreateActiveForToken(string $token): Cart
    {
        return Cart::query()->firstOrCreate([
            'token' => $token,
            'status' => 'active',
        ]);
    }
}
