<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTelegramId extends Model
{
     protected $fillable = [
        'user_id', 'telegram_id', 'phone_number', 'chat_id',
         
    ];
}
