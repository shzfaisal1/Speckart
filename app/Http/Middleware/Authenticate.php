<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Auth\AuthenticationException;

class Authenticate extends Middleware
{
    protected function authenticate($request, array $guards)
{
    \Log::info('Authorization Header:', [$request->header('Authorization')]);

    if (empty($request->header('Authorization'))) {
        $this->unauthenticated($request, $guards);
    }

    parent::authenticate($request, $guards);
}


   protected function unauthenticated($request, array $guards)
{
    throw new \Illuminate\Auth\AuthenticationException('Custom Unauthenticated Message');
}

}
