<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Http\Request;
use Modules\User\Entities\User;

class UserController
{
    public function index()
    {
        return User::first();
    }
}
