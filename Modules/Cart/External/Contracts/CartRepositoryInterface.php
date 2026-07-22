<?php

namespace Modules\Cart\External\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Cart\Entities\Cart;
use Modules\Core\External\Repositories\Contract\BaseRepositoryInterface;

interface CartRepositoryInterface extends BaseRepositoryInterface
{
    public function getOrCreateActiveForUser(Authenticatable $user): Cart;

    public function getOrCreateActiveForToken(string $token): Cart;

    public function findActiveForUser(Authenticatable $user): ?Cart;

    public function findActiveForToken(string $token): ?Cart;
}
