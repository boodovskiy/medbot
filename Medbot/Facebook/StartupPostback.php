<?php

namespace App\Medbot\Facebook;

use Casperlaitw\LaravelFbMessenger\Contracts\PostbackHandler;
use Casperlaitw\LaravelFbMessenger\Messages\ReceiveMessage;
use Casperlaitw\LaravelFbMessenger\Messages\ButtonTemplate;

class StartupPostback extends PostbackHandler
{
  // If webhook get the $payload is `start` will run this postback handler
  protected $payload = '^start_*(?P<id>[0-9]*)'; // use regex

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

      case 'start':

        $button = new ButtonTemplate($message->getSender(), 'Default text');
        $button
          ->setText('In the end of our discussion I will guide you to the nearest appropriate doctor. I will also recommend some medicines to lower your pain and other symptoms. This DOES NOT mean you don’t have to go to the real doctor')
          ->addPostBackButton('I got it', "start_1");
        $this->send($button);

        break;

      case 'start_1':

        $button = new ButtonTemplate($message->getSender(), 'Default text');
        $button
          ->setText('Besides you will get recommendations on what medical tests and researches you have to conduct and where you can do it. It will help you to save time and budget. Your doctor will already have everything to set the diagnosis and begin the treatment.')
          ->addPostBackButton('I got it', "start_2");
        $this->send($button);

        break;

      case 'start_2':

        $button = new ButtonTemplate($message->getSender(), 'Default text');
        $button
          ->setText('Do you fell pain in the stomach?')
          ->addPostBackButton('Yes', "question_1_yes")
          ->addPostBackButton('No', "question_1_no");
        $this->send($button);

        break;

    }
  }

}
