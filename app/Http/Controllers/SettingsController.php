<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use App\UserSettings;
use App\CurrencyLookup;

class SettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $user_settings = $user->userSettings;
        
        $code = "";
        
        if($user_settings)
        {
            $code = $user_settings->currencyLookup->code;
        }
        
        $currencies = CurrencyLookup::all();
        
        return view('bot-config', [
            'code'=>$code, 
            'currencies'=>$currencies
                ])->render();
        
       
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    
    public function update_currency(Request $request)
    {
        $user = Auth::user();
        $user_settings = $user->userSettings;
        
        $settings = "";
        
        if($user_settings)
        {
            $settings = UserSettings::find($user_settings->id);
            
        }
        else
        {
            $settings = new UserSettings();
            $settings->user_id = $user->id;
        }
        
        
        $settings->currency_id = $request->currency;

        $settings->save();
        
        return 'Success';
    }
    
    public function store(Request $request)
    {
       // Validate the request...
        
        
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
