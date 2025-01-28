<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function ensureAdmin()
    {
        if (!auth()->user()->is_admin) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Unauthorized');
        }
    }
}
