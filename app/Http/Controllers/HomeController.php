<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\CurrencyLookup;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $user = Auth::user();
        
        $userid = $user->id;
       
        $user_telegram = $user->userTelegramId;
        $user_settings = $user->userSettings;
        
        $telegram_id = "";
        $code = "";
        $country = "";
        
        if($user_telegram)
        {
            $telegram_id = $user_telegram->telegram_id;
        }
        
        if($user_settings)
        {
            $code = $user_settings->currencyLookup->code;
            $country = $user_settings->currencyLookup->description;
        }
        
        $currencies = CurrencyLookup::all();
        
        return view('home', [
            'user_id' => $userid, 
            'telegram_id' => $telegram_id, 
            'code'=>$code, 
            'country'=>$country,
            'currencies'=>$currencies
                ])->render();
    }
}
