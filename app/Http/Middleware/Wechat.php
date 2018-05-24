<?php

namespace App\Http\Middleware;

use App\Exceptions\ApiException;
use App\Jobs\UserLogs;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class Wechat extends BaseMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     * @throws ApiException
     */
    public function handle($request, Closure $next)
    {
        Log::info('我到了wechat中间件');

        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                throw new ApiException('无权限用户',4004);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            throw new ApiException('token过期',4001);
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            throw new ApiException('token非法',4000);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            throw new ApiException('token缺失',5000);
        }

        //$user = User::where(User::FIELD_ID_OPENID,$user->{User::FIELD_ID_OPENID})->first();

        //dispatch((new UserLogs($user))->onQueue('record_visit_log'));

        $request->offsetSet('user',$user);

        return $next($request);
    }
}
