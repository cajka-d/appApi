<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RemoteApiAccessKey
{
    public function handle(Request $request, Closure $next)
    {
        $provided = $request->header('X-Access-Key', $request->query('access_key'));
        $expected = (string) config('services.remote_api.access_key');

        if ($expected === '' || !is_string($provided) || !hash_equals($expected, (string) $provided)) {
            return response()->json([
                'error' => 'closed',
                'message' => 'Доступ закрыт',
            ], 403);
        }

        return $next($request);
    }
}
