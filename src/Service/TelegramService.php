<?php

namespace App\Service;

class TelegramService
{
    //TODO одном метода достаточно, решил не подключать лишние библиотеки
    private static string $botToken = '8026108479:AAFx-ob__a2o_dgW0bXyziXUxMDgFfQmvIQ';
    // бот https://t.me/myAiMPbot


    public static function sendMessage(string $chatId, string $message): array
    {
        // Реализация отправки в Telegram
        $url = 'https://api.telegram.org/bot' .self::$botToken . '/sendMessage';

        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
