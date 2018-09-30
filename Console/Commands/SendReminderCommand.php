<?php

namespace App\Console\Commands;

use App\Reminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class SendReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendReminderCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending reminders to patients';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $reminders = Reminder::whereHas('appointment', function ($q) {
            $q->where('working', 1);
        })->get();

        foreach ($reminders as $reminder) {
            $date = Carbon::parse($reminder->time);
            $now = Carbon::now();
            if ($date->diffInMinutes($now) < 1 && $now->gte($date)) {
                $socials = $reminder->appointment->patient->social;
                $text = $reminder->appointment->text;
                foreach ($socials as $social) {
                    switch ($social->provider) {
                        case 'telegram':
                            Telegram::sendMessage([
                                'chat_id' => $social->provider_patient_id,
                                'text' => $text
                            ]);
                            break;
                        case 'facebook':

                            break;
                    }
                }
                $reminder->delete();
            }
        }
    }
}
