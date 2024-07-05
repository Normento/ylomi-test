<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailObject extends Notification
{
    public function __construct(
        public string $subject = "Default subject",
        public string $title = "Default Title",
        public string $preheeader = "Default email preheader",
        public string $intro = "",
        public string $corpus = "",
        public string $outro = "",
        public string $template = "email.default",
        public array $data = [],
        public array $files = []
    ) {

    }

}
