<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\User;
use Illuminate\Http\Request;
use App\MonitorLog;

class MonitoringLog
{
    private $log;
    private $user;
    private $auth;
    private $route;
    private $request;
    private $monitorLog;

    public function __construct(Log $log, User $user, Auth $auth, Route $route, Request $request, MonitorLog $monitorLog)
    {
        $this->log = $log;
        $this->user = $user;
        $this->auth = $auth;
        $this->route = $route;
        $this->request = $request;
        $this->monitorLog = $monitorLog;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // if (auth()->check()) {
        //     $data = $this->request->all();
        //     $user = $this->auth::user();
        //     $case = $this->route::currentRouteName().'.'.strtolower($this->request->method());
        //     switch ($case) {
        //         case 'my-course.show.get':
        //             $logs = MonitorLog::create([
        //                 'user_id' => $user->id,
        //                 'ip_address' => $request->ip(),
        //                 'item_id' => 0,
        //                 'type' => 'login',
        //                 'type_detail' => 'LOG_LOGIN',
        //             ]);
        //         case 'home.get':
                    
        //     }
        // }
        return $next($request);
    }
}
