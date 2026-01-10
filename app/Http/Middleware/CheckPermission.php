<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = auth()->user();

        if (! $user || ! $user->hasPermission($permission)) {
            abort(403, __('Bạn không có quyền truy cập.'));
        }

        return $next($request);
    }
}
