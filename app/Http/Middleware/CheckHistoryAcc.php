<?php

namespace App\Http\Middleware;

use App\Jobs\GetHistoryAccountFullInCache;
use App\Jobs\GetHistoryAccountUpdateInCache;
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
            $checkResult = self::doCheck($acc);
            $request->acc = $acc;
            $params = $request->all();
            $params['acc']=$acc;
            if ($checkResult['result']==false){
                return response(view(getenv('BCH_API').'.process-tranz', ['account' => $acc,'total'=>$checkResult['max'],'current'=>$checkResult['processed'] ]));
            }
        }
        return $next($request);
    }

    public static function doCheck($acc)
    {
        $result = ['result'=>true];
        $acc = str_replace('@', '', $acc);
        $acc = mb_strtolower($acc);
        $acc = trim($acc);

        $max = BchApi::getHistoryAccountLast($acc);
        //$current = BchApi::getCurrentProcessedHistoryTranzId($acc);
        $processed = BchApi::getCurrentProcessedHistoryTranzIdInDB($acc);
//            dump($max,$processed);

        if ($processed == 0){
            //Artisan::call('BchApi:GetHistoryAccountFullInCache',['api'=>'golos','acc'=>$acc]);
            //GolosApi::getHistoryAccountFullInCache($acc);

            //dispatch(new GetHistoryAccountFullInCache($acc, getenv('BCH_API')))->onQueue(getenv('BCH_API').'CheckHistoryAcc');
            dispatch(new GetHistoryAccountFullInCache($acc, getenv('BCH_API')))->onQueue(getenv('BCH_API').'full_load');

            //return redirect()-
            $collection = BchApi::getMongoDbCollection($acc);
            $processed = $collection->count();

            $result['result']=false;
            $result['max'] = $max;
            $result['processed'] = $processed;


        }elseif ($max-$processed>0) {

            dispatch(new GetHistoryAccountUpdateInCache($acc,$processed, getenv('BCH_API')))->onQueue(getenv('BCH_API').'update_load');
            //dispatch(new GetHistoryAccountFullInCache($acc, getenv('BCH_API')))->onQueue('full_load');
            /*if (!$request->has('skip'))
            return response(view(getenv('BCH_API').'.process-tranz', ['account' => $acc,'total'=>$max,'current'=>$processed ]));*/
            sleep(5);
            $result['result']=true;

        }
        return $result;


    }
}
