<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerDBSwitch
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if($request->header('region'))
        {
            $region = $request->header('region');

            if($region == 'Default')
            {
                DB::setDefaultConnection('mongodbCustomer');
                DB::reconnect('mongodbCustomer');
    
                return $next($request);
            }

            if($region == 'Europe')
            {
                DB::setDefaultConnection('mongodbCustomer');
                DB::reconnect('mongodbCustomer');
    
                return $next($request);
            }

            else if($region == 'North America')
            {
                DB::setDefaultConnection('mongodbCustomer');
                DB::reconnect('mongodbCustomer');
    
                return $next($request);
            }

            else if($region == 'None')
            {
                return $next($request);
            }

            else
            {
                return response()->json(['status'=>false,'error'=>"Invalid Region In Api Header!"], 503);
            }
        }
        else
        {
            return response()->json(['status'=>false,'error'=>"Region is required in request header!"], 503);
            // return $next($request);
        }
    }
}
