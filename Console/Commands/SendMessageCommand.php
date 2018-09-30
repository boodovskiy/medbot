<?php

namespace App\Console\Commands;

use App\Message;
use Illuminate\Console\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class SendMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SendMessageCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sending message to patients';

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
        $messages = Message::where('status', 'wait')->where('marker', 'current')->get();
        foreach ($messages as $message) {
            if (count($message->patient->social)) {
                foreach ($message->patient->social as $social) {
                    switch ($social->provider) {
                        case 'telegram':
                            Telegram::sendMessage([
                                'chat_id' => $social->provider_patient_id,
                                'text' => $message->text
                            ]);
                            $message->update(['status' => 'sent']);
                            break;
                        case 'facebook':
                            /*Отправка вопроса на Facebook*/
                            break;
                    }
                }
            }
        }
    }
}
