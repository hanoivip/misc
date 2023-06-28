<?php

namespace Hanoivip\Misc\Middlewares;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Request;

class LockByUser
{
    public function handle(Request $request, Closure $next, $timeout = 10, $wait = 5)
    {
        if (Auth::check())
        {
            $lockKey = "LockByUser" . Auth::user()->getAuthIdentifier();
            $lock = Cache::lock($lockKey, $timeout);
            try 
            {
                $lock->block($wait);
                $response = $next($request);
            } catch (LockTimeoutException $e) {
                abort(500, "Route is locked");
            } finally {
                optional($lock)->release();
            }
            return $response;
        }
        else
        {
            return $next($request);
        }
    }
}
