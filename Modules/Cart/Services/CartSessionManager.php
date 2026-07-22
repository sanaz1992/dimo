<?php

namespace Modules\Cart\Services;

use Illuminate\Support\Str;

class CartSessionManager
{
    public function getOrCreateToken(): string
    {
        $token = session()->get('cart_token');

        if (! $token) {
            $token = (string) Str::uuid();
            session()->put('cart_token', $token);
        }

        return $token;
    }
}
