<?php
/**
 * Created by PhpStorm.
 * User: MAX
 * Date: 24.03.2017
 * Time: 17:10
 */
namespace app\Medbot\Telegram\Commands;

use App\Social;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Laravel\Facades\Telegram;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Старт работы";

    /**
     * @inheritdoc
     */
    public function handle($arguments)
    {
        $update = Telegram::getWebhookUpdates();
        $chat_id = $update->getMessage()->getChat()->getId();
        if (!empty(Social::where('provider_patient_id', $chat_id)->first())) {
            $this->replyWithMessage(['text' => 'Мы рады что вы вернулись']);
            $this->replyWithChatAction(['action' => Actions::TYPING]);
        } else {
            $this->replyWithMessage(['text' => 'Введите промокод, который выдал Вам доктор:']);
            $this->replyWithChatAction(['action' => Actions::TYPING]);
        }
    }
}