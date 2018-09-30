<?php

namespace App\Http\Controllers;

use App\Events\ReceivedPatientScheduleTimeEvent;
use App\Message;
use App\Promocode;
use App\Social;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{
    public function callback(Request $request)
    {
        $update = Telegram::commandsHandler(true);
        //$update = Telegram::getWebhookUpdates();
        $chat_id = $update->getMessage()->getChat()->getId();
        $text = $update->getMessage()->getText();
        /*Если сообщение не команда*/
        if (isset($text) && !starts_with($text, '/')) {
            if ($this->checkSocialBinding($chat_id)) {
                $patient = $this->getPatient($chat_id);
                $social = $this->getSocial($chat_id);
                $message = Message::where('patient_id', $patient->id)->where('status', 'sent')->where('marker', 'current')->first();
                if (count($message)) {
                    event(new ReceivedPatientScheduleTimeEvent($social, $text));
                } else {
                    Telegram::sendMessage([
                        'chat_id' => $social->provider_patient_id,
                        'text' => 'Все хорошо'
                    ]);
                }

            } else {
                $this->checkPromocode($chat_id, $text);
            }
        }
    }

    private function checkSocialBinding($chat_id)
    {
        if (!empty(Social::where('provider', 'telegram')->where('provider_patient_id', $chat_id)->first())) {
            return true;
        } else {
            return false;
        }
    }

    private function checkPromocode($chat_id, $text)
    {
        $promocode = Promocode::where('value', $text)->first();
        if (empty($promocode)) {
            $response = Telegram::sendMessage([
                'chat_id' => $chat_id,
                'text' => 'Вы ввели неверный промокод' . PHP_EOL . 'Введдите ваш промокод'
            ]);
        } else {
            Social::create(['patient_id' => $promocode->patient_id, 'provider_patient_id' => $chat_id, 'provider' => 'telegram']);
            $promocode->delete();
            $response = Telegram::sendMessage([
                'chat_id' => $chat_id,
                'text' => 'Вы успешно авторизировались!'
            ]);
        }
    }

    private function getPatient($chat_id)
    {
        $social = Social::where('provider_patient_id', $chat_id)->first();
        return $social->patient;
    }

    private function getSocial($chat_id)
    {
        $social = Social::where('provider_patient_id', $chat_id)->first();
        return $social;
    }


    private function ololo()
    {
        /*     Telegram::sendMessage([
                   'chat_id' => $chat_id,
                   'text' => 'Ответ: ' . $text]);

               $keyboard = [
                   ['yes', 'no'],
               ];

               $reply_markup = Telegram::replyKeyboardMarkup([
                   'keyboard' => $keyboard,
                   'resize_keyboard' => true,
                   'one_time_keyboard' => true
               ]);

               $response = Telegram::sendMessage([
                   'chat_id' => $chat_id,
                   'text' => $text,
                   'reply_markup' => $reply_markup
               ]);

               $messageId = $response->getMessageId();*/
    }
}
