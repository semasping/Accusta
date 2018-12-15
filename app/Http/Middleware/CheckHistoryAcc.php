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

    private static function doCheckAcc($acc)
    {
        if (BchApi::getFullAccount($acc)){
            return true;
        };
        return false;
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
        if ($request->acc) {

            $acc = ($request->acc);
            if (!self::doCheckAcc($acc)){
                return response(view(getenv('BCH_API').'.no-account', ['account' => $acc, 'form_action'=>$request->get('controller','AuthorRewardsController@showAll')]));
            };

            $checkResult = self::doCheck($acc);
            $request->acc = $acc;
            $params = $request->all();
            $params['acc']=$acc;
            if ($checkResult['result']==false){
                return response(view(getenv('BCH_API').'.process-tranz', ['account' => $acc,'total'=>$checkResult['max'],'current'=>$checkResult['processed'] ]));
            }
            if ($checkResult['result']==='wait updates'){
                return response(view(getenv('BCH_API').'.wait-update-tranz', ['account' => $acc]));
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
        $processed = BchApi::getCurrentProcessedHistoryTranzIdInDB($acc);

        if ($processed == 0){

            dispatch(new GetHistoryAccountFullInCache($acc, getenv('BCH_API')))->onQueue(getenv('BCH_API').'full_load');

            $collection = BchApi::getMongoDbCollection($acc);
            $processed = $collection->count();

            $result['result']=false;
            $result['max'] = $max;
            $result['processed'] = $processed;
        }elseif ($max-$processed>0) {

            dispatch(new GetHistoryAccountUpdateInCache($acc,$processed, getenv('BCH_API')))->onQueue(getenv('BCH_API').'update_load');

            $result['result']='wait updates';
        }
        return $result;


    }
}
