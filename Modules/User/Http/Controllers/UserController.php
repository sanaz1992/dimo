<?php

namespace Modules\User\Http\Controllers;

use Modules\User\Entities\User;

class UserController
{
    public function index()
    {
        return User::first();
    }
}
