<?php

namespace Hanoivip\Misc\Middlewares;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Closure;
use Illuminate\Http\Response;

class CacheResponse
{
    /**
     * 
     * @param Request $request
     * @param Closure $next
     * @param number $expires Expiration from now, in minutes
     */
    public function handle(Request $request, Closure $next, $expires = 30)
    {
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
        
        $requestId = md5($request->getMethod() . '|' . $request->url() . '|' . $inputHash);
        if (Cache::has($requestId))
        {
            //Log::debug("Respones cache hit");
            return Cache::get($requestId);
        }
        else 
        {
            //Log::debug("Respones cache not found.. genearte content..");
            $response = $next($request);
            $hitResponse = $response->header('US-CACHED', 'hit')->getContent();
            Cache::put($requestId, $hitResponse, Carbon::now()->addMinutes($expires));
            return $response->header('US-CACHED', 'missed');
        }
        /*
         * Can not cache API?
        return Cache::remember($requestId, 3600, function () use ($request, $next) {
            Log::debug("Respones cache not found.. genearte content..");
            return $next($request)->getContent();
        });
        */
    }
}