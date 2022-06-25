<?php

namespace App\Notifications;

class TelegramNotifications 
{
      public function send($message, $receiver)
    {
        $apiToken = "5491722985:AAHJ3tpdfWzQP2VUiqJvJcmgjhK55Dz80Sg";
        
        if($receiver == 'data') $chat_id =   "-1001741824435";
        if($receiver == 'operation') $chat_id =   "-1001624659026";

       

       $data = [
	            "chat_id" => $chat_id,
                'text' => $message, 
                'parse_mode' => 'markdown'
       ];


       file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
    }
}
