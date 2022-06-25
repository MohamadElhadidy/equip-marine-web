<?php

namespace App\Notifications;

class WhatsappNotifications
{
    public function send($message)
    {
        $instance ='instance289321';
        $token = 'jq8uqjau54shlok1';
        
        $chat_id =   "120363040368029161@g.us";

        $data = [
            "chatId" => $chat_id,   
            "body" => $message, 
        ];
        $json = json_encode($data); 
        $url = 'https://api.chat-api.com/' . $instance . '/message?token=' . $token;
        $options = stream_context_create(['http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => $json,
        ],
        ]);

        file_get_contents($url, false, $options);
    }
}