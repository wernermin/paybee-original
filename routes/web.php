<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group( ['middleware' => 'auth' ], function() { 
    Route::get('/home', 'HomeController@index');
    Route::get('/me', 'ApiController@me');
    Route::get('/updates','ApiController@updates');
    Route::get('/respond/{text?}/{cur?}/{amount?}', [
        'uses' => 'ApiController@respond',
        ]);
    
    Route::get('/bot-config', 'SettingsController@index');
    Route::post('/bot-config', 'SettingsController@index');
    Route::get('/update_currency/{currency}', [
        'uses' => 'SettingsController@update_currency',
        ]);
    
});
//Route::get('/setWebHook','ApiController@setWebHook');
//Route::post('/my-bot-token/webhook', 'ApiController@setWebHook');

