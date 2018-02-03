<?php

namespace App\Http\Middleware;

use App\Jobs\GetHistoryAccountFullInCache;
use App\semas\GolosApi;
use Closure;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Artisan;

class CheckHistoryAcc
{
    use DispatchesJobs;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->acc) {
            $acc = ($request->acc);
            $max = GolosApi::getHistoryAccountLast($acc);
            $current = GolosApi::getCurrentProcessedHistoryTranzId($acc);
            if ($current < $max - 2000){
                //Artisan::call('BchApi:GetHistoryAccountFullInCache',['api'=>'golos','acc'=>$acc]);
                //GolosApi::getHistoryAccountFullInCache($acc);
                dispatch(new GetHistoryAccountFullInCache($acc))->onQueue('CheckHistoryAcc');
                $params = $request->all();
                $params['acc']=$acc;
                //return redirect()->action('TransAccController@showProcessTranz',$params);
                return response(view('trans.process-tranz', ['account' => $acc,'total'=>$max,'current'=>$current ]));
            }


        }
        return $next($request);
    }
}
