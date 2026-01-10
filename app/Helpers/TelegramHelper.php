<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class TelegramHelper
{
   public static function sendMessage($message, $bot = 'default')
    {
        switch ($bot) {
            case 'kyc':
                $token = config('telegram.bot_token_kyc');
                $chatId = config('telegram.chat_id_kyc');
                break;

            case 'default':
            default:
                $token = config('telegram.bot_token');
                $chatId = config('telegram.chat_id');
                break;
        }
        Http::get("https://api.telegram.org/bot" . $token . "/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML',
        ]);
        
    }
}
