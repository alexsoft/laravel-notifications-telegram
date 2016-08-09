# Laravel 5.3 Notifications to Telegram

## Installation

[PHP](https://php.net) 5.6.4+ is required.

To get the latest version of Laravel Notifications Telegram, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require alexsoft/laravel-notifications-telegram
```

Or you can manually update your require block and run `composer update` if you choose so:

```json
{
    "require": {
        "alexsoft/laravel-notifications-telegram": "^0.1"
    }
}
```

You will also need to install `guzzlehttp/guzzle` http client to send request to Telegram API.

Once Laravel Notifications Telegram is installed, you need to register the service provider. Open up `config/app.php` and add the following to the `providers` key.

* `Alexsoft\LaravelNotificationsTelegram\ServiceProvider::class`

## Configuration

### Telegram Bot API Token
First, talk to [@BotFather](https://core.telegram.org/bots#botfather) and generate one.
Then put it to `config/services.php` configuration file. You may copy the example configuration below to get started:
```
'telegram-notifications-bot-token' => [
    'key' => env('TELEGRAM_BOT_API_TOKEN')
]
```

### Routing Telegram notifications
In order to send notifications to telegram, you need specify Telegram chat_id of notifiable entity. To provide library with correct chat id, you need to define `routeNotificationForTelegram` method on the entity:

```php
<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Route notifications for the Nexmo channel.
     *
     * @return string
     */
    public function routeNotificationForTelegram()
    {
        return $this->telegram_user_id;
    }
}
```

### Usage

#### `via` method
On notification entity just add `'telegram'` item to array that is returned from `via` method.

#### `toTelegram` method
Also you should define `toTelegram` method on notifications class. This method will receive a `$notifiable` entity and should return a `Alexsoft\LaravelNotificationsTelegram\TelegramMessage` instance.
Telegram messages may contain lines of text as well as a "call to action", just like Mail notification messages that are available in Laravel out of the box.

```php
/**
 * Get the telegram representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \Alexsoft\LaravelNotificationsTelegram\TelegramMessage
 */
public function toMail($notifiable)
{
    $url = url('/invoice/' . $this->invoice->id);

    return (new TelegramMessage)
        ->line('One of your invoices has been paid!')
        ->action('View Invoice', $url)
        ->line('Thank you for using our application!');
}
```

#### Success, info or error?
Telegram notifications also support success and error notifications.

## License

Laravel Notifications Telegram is licensed under [The MIT License (MIT)](LICENSE).
