<?php

namespace App\Http\Middleware;

use App\Jobs\GetHistoryAccountFullInCache;
use App\semas\BchApi;
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
            $max = BchApi::getHistoryAccountLast($acc);
            $current = BchApi::getCurrentProcessedHistoryTranzId($acc);
            if ($current < $max - 2000){
                //Artisan::call('BchApi:GetHistoryAccountFullInCache',['api'=>'golos','acc'=>$acc]);
                //GolosApi::getHistoryAccountFullInCache($acc);

                dispatch(new GetHistoryAccountFullInCache($acc, getenv('BCH_API')))->onQueue(getenv('BCH_API').'CheckHistoryAcc');
                $params = $request->all();
                $params['acc']=$acc;
                //return redirect()->action('TransAccController@showProcessTranz',$params);

                return response(view(getenv('BCH_API').'.process-tranz', ['account' => $acc,'total'=>$max,'current'=>$current ]));
            }


        }
        return $next($request);
    }
}
