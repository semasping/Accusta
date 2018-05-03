<?php
/**
 * Created by PhpStorm.
 * User: semasping (semasping@gmail.com)
 * Date: 16.08.2017
 * Time: 13:10
 */

namespace App\semas;


use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class AdminNotify
{

    public static function send($text, $full=false)
    {
        try {
            if ($full){
                $trace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,1);
                $text = 'Full '.print_r($trace,true) .' '.$text;
            }
            $text = '#'.env('APP_ENV').' : '.env('APP_NAME').' '.$text;

            //Log::channel('slack')->info($text);

            Telegram::setAccessToken(getenv('TELEGRAM_BOT_TOKEN'))->sendMessage([
                    'chat_id' => '147893636',
                    'text'    => $text,
                ]);
        }catch (\Exception $e){
            //echo $e->getMessage();
        }
    }
}