<?php

namespace Alexsoft\LaravelNotificationsTelegram;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\Notification;

class TelegramChannel
{
    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * Telegram bot API key
     *
     * @var string
     */
    protected $telegramBotKey;

    /**
     * Create a new Slack channel instance.
     *
     * @param  \GuzzleHttp\Client  $http
     * @param  string  $telegramBotKey
     */
    public function __construct(HttpClient $http, $telegramBotKey)
    {
        $this->http = $http;
        $this->telegramBotKey = $telegramBotKey;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $chatId = $notifiable->routeNotificationFor('telegram')) {
            return;
        }

        $message = $notification->toTelegram($notifiable);

        $url = 'https://api.telegram.org/bot' . $this->telegramBotKey . '/sendMessage';

        $text = '<b>' . ($message->level == 'error' ? 'Whoops!' : 'Hello!') . '</b>' . "\n\n";

        $text .= implode("\n", $message->introLines) . "\n";

        if (isset($message->actionText)) {
            $text .= '<a href="' . $message->actionUrl .'">' . $message->actionText . '</a>';
        }

        $text .=  "\n" . implode("\n", $message->outroLines);

        $text .= "\n\nRegards,\n" . config('app.name');

        $telegramMessage = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false
        ];

        // Send notification to the $notifiable instance...
        $this->http->post($url, [
            'form_params' => $telegramMessage
        ]);
    }
}