<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Ixudra\Curl\Facades\Curl;
use Auth;
use App\UserTelegramId;

class ApiController extends Controller
{
    //
    public function me(){
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $response = $telegram->getMe();
        return $response;
    }
    
    public function updates(){
        
        
        //save user requests to table
        //get last request user entry 
        //check if last response is = this user newest response
        //if not update else leave
        
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $response = $telegram->getUpdates();
        
        $user = Auth::user();
        
        $user_telegram = $user->userTelegramId;
        $telegram_id = "";
        $chatid = "";
        $latest_text = "";
        $user_resquests = array();
        
        if($user_telegram)
        {
            $telegram_id = $user_telegram->telegram_id;
            $chatid = $user_telegram->chat_id;
            
            foreach($response as $latest)
            {
                $utext = $latest['message']['text'];
                $utid = $latest['message']['from']['id'];

               // print_r($latest);
               if($utid == $telegram_id)
               {
                   array_push($user_resquests,$utext);
               }
            }
            $latest_text = end($user_resquests);
            
            if(strstr($latest_text,'linkAccount'))
            {
                $latest_text = "";
            }
        }
        else
        {
            $request = collect(end($response));
            $latest_text = $request['message']['text'];
            
            if(!strstr($latest_text,'linkAccount'))
            {
                $latest_text = "";
            }
            
            //print_r($request);
        }
        
        
        
        
       // print $latest_text;
        return $latest_text;
       
       // $request = collect(end($response));
       // $text = $request['message']['text'];
        
    }
    
    
    public function respond($text = false, $currency = false, $amount = false){
        
        $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $user = Auth::user();
        
        $user_telegram = $user->userTelegramId;
        $telegram_id = "";
        $chatid = "";
        
        if($user_telegram)
        {
            $telegram_id = $user_telegram->telegram_id;
            $chatid = $user_telegram->chat_id;
        }
        else
        {
            $response = $telegram->getUpdates();
            $request = collect(end($response));
            $telegram_id = $request['message']['from']['id'];
            $chatid = $request['message']['chat']['id'];
        }
        
        if($currency == "")
        {
            $currency = false;
        }
        
        if($amount == "")
        {
            $amount = 1;
        }
        
        
        //$response = $telegram->getUpdates();
        //$request = collect(end($response));
        
        //loop through updates and get last request for this user to send to correct channel
        
        //$telegram_id = $request['message']['from']['id'];
        //$chatid = $request['message']['chat']['id'];
        //$text = $request['message']['text'];
        /*
        print "<pre>";
        print_r($request);
        print "</pre>";
        die();*/
        switch($text) {
            case '/start':
                $this->showMenu($telegram, $chatid);
                break;
            case '/menu':
                $this->showMenu($telegram, $chatid);
                break;
            case 'getBTC';
                $this->getBTC($telegram, $chatid, $currency, $amount);
                break;
            case 'getUserID';
                $this->getUserID($telegram, $chatid);
                break;            
            case 'linkAccount';
                $this->linkAccount($telegram, $chatid,$telegram_id);
                break;
            default:
                $info = 'I do not understand what you just said. Please choose an option';
                $this->showMenu($telegram, $chatid, $info);
        }
    }
    
    public function linkAccount($telegram, $chatid, $telegram_id){
        
        $user = Auth::user();
        $userid = Auth::user()->id;
        
        //save the user account id
        
        $actiont = "";
        
        //if($userid->lesson_id != "")
        //{
            //update
          //  $lesson = Lesson::find($request->lesson_id);
            //$actiont = "update";
       // }
        //else
        //{
            //create new
        
        $user_telegram = $user->userTelegramId;
        if($user_telegram)
        {
            $response = $telegram->sendMessage([
                'chat_id' => $chatid,
                'text' => 'Your account has already been linked! Type /getUserID OR /getBTC'
            ]);
        }
        else
        {
            $userTelegramId = new UserTelegramId();
            //}

            $userTelegramId->user_id = $userid;
            $userTelegramId->telegram_id = $telegram_id;
            $userTelegramId->chat_id = $chatid;

            $userTelegramId->save();


            $response = $telegram->sendMessage([
                'chat_id' => $chatid,
                'text' => 'Your account has been linked! Type /getUserID OR /getBTC'
            ]);
        }
    }
    
    public function showMenu($telegram, $chatid, $info = null){
        $message = '';
        if($info !== null){
            $message .= $info.chr(10);
        }
        $message .= '/getBTC'.chr(10);
        $message .= '/getUserID'.chr(10);
        
        $response = $telegram->sendMessage([
            'chat_id' => $chatid, 
            'text' => $message
        ]);
    }
 
   
    
    public function getBTC($telegram, $chatid, $cur = false, $amount = false){
        $message = '';
        
        //get user default currency here
        
        
        
        
        if($cur == false)
        {
            //get user default currency here
            
            $cur = "USD";
        }
        
        
        
       
        $btc = $this->getCurl($cur, $amount);
        
        $message = $btc;
        
        $response = $telegram->sendMessage([
            'chat_id' => $chatid,
            'text' => $message
        ]);
    }
    
    public function getUserID($telegram, $chatid){
        
        $userid = Auth::user()->id;
        
        $response = $telegram->sendMessage([
            'chat_id' => $chatid,
            'text' => "Your paybee user ID is: ".$userid
        ]);
    }
    
    public function getCURL($cur, $amount)
    {
        //https://telegram.me/wpaybee_bot
        
        //get all currencies
        
        
        //check if currency is valid
        
        /*
        $url = "https://api.coindesk.com/v1/bpi/supported-currencies.json";
        
        $reponse = "";
        
       
        
        */
        $message = "";
        $url = "https://api.coindesk.com/v1/bpi/currentprice/".$cur.".json";
        
        $reponse = "";
        
       
        $response = Curl::to($url)->get();
        
        
        
        if(strpos($response,'Sorry') == "")
        {
            $cur_data = json_decode($response,true);

            $btc_to_cur = $amount / $cur_data['bpi'][$cur]['rate_float'];

            $btc_to_cur = number_format((float) $btc_to_cur, 6, '.', '');

            $cur_to_btc = $cur_data['bpi'][$cur]['rate_float'];

            $message = $amount." ".$cur." is ".$btc_to_cur." BTC (".$cur_to_btc." ".$cur." - 1 BTC)";
        }
        else
        {
            $message = $response;
            $message = "Still going in here";
        }
        
        return $message;
    }
    /*
    public function setWebHook(){
        $telegram = new Api(env('TELEGRAM-BOT-TOKEN'));
 
        $response = $telegram->setWebhook(['url' => 'https://paybee.com/my-bot-token/webhook']);
 
        return $response;
        
    }
    
    
    

    public function webhook(Request $request){
            $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));

            $chatid = $request['message']['chat']['id'];
            $text = $request['message']['text'];

            switch($text) {
                    case '/start':
                            $this->showMenu($telegram, $chatid);
                            break;
                    case '/menu':
                        $this->showMenu($telegram, $chatid);
                        break;
                    case '/website':
                            $this->showWebsite($telegram, $chatid);
                            break;
                    case '/contact';
                            $this->showContact($telegram, $chatid);
                            break;
                    case '/getUserID';
                    $this->getUserID($telegram, $chatid);
                    break;    
                    default:
                            $info = 'I do not understand what you just said. Please choose an option';
                            $this->showMenu($telegram, $chatid, $info);
            }
    }
     * 
     */
    /*
    public function getCurl(){
        
        //get all currencies
        $response = Curl::to('https://api.coindesk.com/v1/bpi/supported-currencies.json')->get();
        
        return $response;
    }
    */
    
    ///getBTCEquivalent 30 USD
    
    //https://api.coindesk.com/v1/bpi/supported-currencies.json
    
    /*
     * CURL Post Request:

        public function getCURL()
        {
            $response = Curl::to('https://example.com/posts')
                        ->withData(['title'=>'Test', 'body'=>'sdsd', 'userId'=>1])
                        ->post();
            dd($response);
        }
        CURL Put Request:

        public function getCURL()
        {
            $response = Curl::to('https://example.com/posts/1')
                        ->withData(['title'=>'Test', 'body'=>'sdsd', 'userId'=>1])
                        ->put();
            dd($response);
        }
        CURL Patch Request:

        public function getCURL()
        {
            $response = Curl::to('https://example.com/posts/1')
                        ->withData(['title'=>'Test', 'body'=>'sdsd', 'userId'=>1])
                        ->patch();
            dd($response);
        }
        CURL Delete Request:

        public function getCURL()
        {
            $response = Curl::to('https://example.com/posts/1')
                        ->delete();
            dd($response);
        }
     */
    
}
