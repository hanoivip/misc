<?php

namespace Hanoivip\Misc\Middlewares;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Closure;

class CacheResponseByUser
{
    public function handle(Request $request, Closure $next, $expires = 30)
    {
        if (Auth::check())
        {
            $userId = Auth::user()->getAuthIdentifier();
            $inputHash = '';
            $data = $request->all();
            if (!empty($data))
            {
                $raw = '';
                sort($data);
                foreach ($data as $k => $v)
                {
                    $raw = $raw . "$k=$v";
                }
                $inputHash = md5($raw);
            }
            
            $requestId = md5($userId . '|' . $request->getMethod() . '|' . $request->url() . '|' . $inputHash);
            if (Cache::has($requestId))
            {
                Log::debug("Respones cache by user $userId hit");
                return Cache::get($requestId);
            }
            else
            {
                Log::debug("Respones cache by user $userId not found.. genearte content..");
                $response = $next($request);
                Cache::put($requestId, $response->getContent(), Carbon::now()->addMinutes($expires));
                return $response->header('US-CACHED', 'missed');
            }
        }
        else
        {
            // just forward
            return $next($request);
        }
    }
}