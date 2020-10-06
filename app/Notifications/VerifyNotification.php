<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Kavenegar\Laravel\Message\KavenegarMessage;
use Kavenegar\Laravel\Notification\KavenegarBaseNotification;

class VerifyNotification extends KavenegarBaseNotification
{
    use Queueable;

    public function __construct()
    {
    }

    public function toKavenegar($notifiable)
    {
        return (new KavenegarMessage())
            ->verifyLookup('verify_first', ['token1', 'token2']);
    }
}
