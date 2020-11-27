<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ApiLog
{
    /**
     * 日志记录路由全局中间件
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $user    = Auth::guard($guard)->user();
        $user_id = 0;
        $table   = '';
        if (!empty($user)) {
            //得到userid
            $user_id = $user->id;
            $table   = Auth::guard($guard)->user()->getTable();
        }
        $start_time = microtime(true);
        $response   = $next($request);
        $rq_time    = microtime(true) - $start_time;
        //插入请求日志
        $request_url = $request->getRequestUri();
        $route       = explode('?', $request_url)[0] ?? '';
        // 不写入日志的接口列表
        $filterRoute = [
            '/api/c1/common/areaList',
        ];
        if (in_array($route, $filterRoute)) return $response;
        $header = $request->header()['authorization'] ?? '';
        if (!empty($header)) $header = json_encode($header);
        DB::table("request_log")
            ->insert([
                'user_id'    => $user_id,
                'table'      => $table,
                'header'     => $header,
                'ip_address' => $request->ip(),
                'method'     => $request->method(),
                'url'        => $request->fullUrl(),
                'param'      => json_encode($request->all()),
                'rq_time'    => sprintf("%.2f", $rq_time),
                'response'   => $response->getContent(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        $total_time = microtime(true) - $start_time;
        file_put_contents(storage_path() . '/logs/rq_time.log', '响应时间+日志时间: ' . $total_time . '  响应时间: ' . $rq_time . '  route: ' . $route . PHP_EOL, FILE_APPEND);
        return $response;
    }
}
