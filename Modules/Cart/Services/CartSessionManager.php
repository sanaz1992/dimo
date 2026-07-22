<?php

namespace Modules\Cart\Services;

class CartSessionManager
{
    private const CART_TOKEN_KEY = 'cart_token';

    public function getToken(): ?string
    {
        return session(self::CART_TOKEN_KEY);
    }

    public function getOrCreateToken(): string
    {
        if (! session()->has(self::CART_TOKEN_KEY)) {
            session([self::CART_TOKEN_KEY => str()->uuid()->toString()]);
        }

        return session(self::CART_TOKEN_KEY);
    }

    public function forget(): void
    {
        session()->forget(self::CART_TOKEN_KEY);
    }
}
