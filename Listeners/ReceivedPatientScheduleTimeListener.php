<?php

namespace App\Listeners;

use App\Event;
use App\Events\ReceivedPatientScheduleTimeEvent;
use App\Message;
use App\Reminder;
use App\Schedule;
use Carbon\Carbon;
use DateTime;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Telegram\Bot\Laravel\Facades\Telegram;

class ReceivedPatientScheduleTimeListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ReceivedPatientScheduleTimeEvent $event
     * @return void
     */
    public function handle(ReceivedPatientScheduleTimeEvent $event)
    {
        $social = $event->social;
        $text = $event->text;
        $patient = $social->patient;
        $message = Message::where('status', 'sent')->where('marker', 'current')->first();
        /*Если пациент написал корректное время*/

        try {
            // $time = Carbon::createFromFormat('H:i', $text);
            $time = new Carbon($text);
            switch ($social->provider) {
                case 'telegram':
                    Telegram::sendMessage([
                        'chat_id' => $social->provider_patient_id,
                        'text' => $message->success
                    ]);
                    $event = Event::find($message->event_id);
                    $schedule = Schedule::create(['patient_id' => $patient->id, 'eventtype_id' => $event->eventtype_id, 'time' => $time]);
                    if ($event) {
                        $appointment = $event->appointment;
                        for ($i = 0; $i < $appointment->length; $i++) {
                            $date = Carbon::createFromFormat('d-m-Y', $appointment->start)->format('Y-m-d');
                            $time = substr($schedule->time, strpos($schedule->time, " ") + 1);
                            $datetime = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $time)->addDays($i * $appointment->periodicity)->addMinutes($event->diff);
                            Reminder::create(['appointment_id' => $appointment->id, 'time' => $datetime->toDateTimeString()]);
                        }
                    }
                    //$message->update(['marker' => '']);
                    $message->delete();
                    $nextMessage = Message::where('patient_id', $patient->id)->where('status', 'wait')->orderBy('created_at', 'asc')->first();
                    if (count($nextMessage)) {
                        $nextMessage->update(['marker' => 'current']);
                    }
                    break;
                case 'facebook':

                    break;
            }
        } catch (\Exception $e) {
            //Если пациент написал лабуду
            switch ($social->provider) {
                case 'telegram':
                    Telegram::sendMessage([
                        'chat_id' => $social->provider_patient_id,
                        'text' => $message->fail
                    ]);
                    break;
                case 'facebook':
                    //Отправка вопроса на Facebook
                    break;
            }
        }

    }
}
