<?php

namespace Alexsoft\LaravelNotificationsTelegram\Exceptions;

use RuntimeException;

class CouldNotSendNotification extends RuntimeException
{
    public function __construct($code, $message)
    {
        parent::__construct("Notifications was not sent. Telegram API responded with `{$code}: {$message}`");
    }

}