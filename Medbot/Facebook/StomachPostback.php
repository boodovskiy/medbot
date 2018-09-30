<?php

namespace App\Medbot\Facebook;

use Casperlaitw\LaravelFbMessenger\Contracts\PostbackHandler;
use Casperlaitw\LaravelFbMessenger\Messages\ReceiveMessage;
use Casperlaitw\LaravelFbMessenger\Messages\ButtonTemplate;

class StomachPostback extends PostbackHandler
{
  // If webhook get the $payload is `question_*` will run this postback handler
  protected $payload = '^question_(?P<id>[1-2]*)'; // use regex

  /**
   * Handle the chatbot message
   *
   * @param ReceiveMessage $message
   *
   * @return mixed
   */
  public function handle(ReceiveMessage $message)
  {
    switch( $message->getPostback() ){

      case 'question_1_yes':

        $button = new ButtonTemplate($message->getSender(), 'Default text');
        $button
          ->setText('Где у Вас болит живот')
          ->addPostBackButton('Сверху', "question_2_top")
          ->addPostBackButton('Снизу', "question_2_bottom")
          ->addPostBackButton('Снизу в подвздошной области', "question_2_lliac");
        $this->send($button);

        break;

      case 'question_2_lliac':

        $button = new ButtonTemplate($message->getSender(), 'Default text');
        $button
          ->setText('Где именно?')
          ->addPostBackButton('Слева', "question_3_left")
          ->addPostBackButton('Справа', "question_3_right");
        $this->send($button);

        break;

    }
  }

}
