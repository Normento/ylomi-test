<?php

namespace App\Notifications;

use App\Mail\DefaultMail;
use Illuminate\Bus\Queueable;
use App\Notifications\MailObject;
use Illuminate\Support\Facades\Mail;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NotificationService extends Notification
{
    protected $recipients = [];

    protected $ccmails = [];
    protected $bccmails = [];

    public function toUsers(array $users)
    {
        $this->recipients = array_map(function ($user) {
            return $user->email;
        }, $users);

        return $this;
    }

    public function toEmails($emails = [])
    {
        if (!empty($emails)) {
            $this->recipients = array_merge($this->recipients, $emails);
        }
        return $this;
    }

    public function withCCMails($ccmails = [])
    {
        if (!empty($ccmails)) {
            $this->ccmails = array_merge($this->ccmails, $ccmails);
        }

        return $this;
    }

    public function withBCCMails($bccmails = [])
    {
        if (!empty($bccmails)) {
            $this->bccmails = array_merge($this->bccmails, $bccmails);
        }

        return $this;
    }

    public function sendMail(MailObject $mailObject)
    {

        foreach ($this->recipients as $recipient) {

            if (filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                Mail::to($recipient)->cc($this->ccmails)->bcc($this->bccmails)->send(new DefaultMail($mailObject));
            }
        }
        return $this;
    }
}
