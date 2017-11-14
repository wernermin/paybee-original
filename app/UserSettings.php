<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSettings extends Model
{
    protected $fillable = ['user_id','currency_id',];
    
    function currencyLookup()
    {
        return $this->belongsTo('App\CurrencyLookup','currency_id','id');
    }
    
}
