<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class GPTController extends Controller
{
    public function getMotivationalMessage ()
    {
        return $this->sendToGPT("Digite em 1 linha em PTBR, uma mensagem motivacional do dia:");
    }

    public function getHelp ($msg)
    {
        return $this->sendToGPT("Responda em PTBR, $msg");
    }

    /**
     * @return mixed|string
     *
     * @author Lucas <lukasccbb@gmail.com>
     */
    private function sendToGPT($msg)
    {
        $openaiEndpoint = "https://api.openai.com/v1/chat/completions";
        $apiKey = env("OPENAI_API_KEY");

        $curl = curl_init();

        $msgToSend = [
            "model" => "gpt-3.5-turbo",
            "messages" => [
                [
                    "role" => "user",
                    "content" => $msg
                ],
            ],
            "temperature" => 0.7
        ];

        curl_setopt_array($curl, array(
            CURLOPT_URL => $openaiEndpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($msgToSend),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        $get_msg = json_decode($response, true);

        curl_close($curl);

        return $get_msg["choices"][0]["message"]["content"] ?? "Desculpe, algo deu errado.";
    }
}
