<?php

namespace Alexsoft\LaravelNotificationsTelegram;

use Alexsoft\LaravelNotificationsTelegram\Events\MessageWasSent;
use Alexsoft\LaravelNotificationsTelegram\Events\SendingMessage;
use Alexsoft\LaravelNotificationsTelegram\Exceptions\CouldNotSendNotification;
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
     * Telegram bot API key.
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

        $shouldNotSendMessage = event(new SendingMessage($notifiable, $notification), [], true) === false;

        if ($shouldNotSendMessage) {
            return;
        }

        $telegramMessage = [
            'chat_id' => $chatId,
            'text' => $this->getText($message),
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
        ];

        // Send notification to the $notifiable instance...
        $response = $this->http->post($this->getTelegramApiUrl(), [
            'form_params' => $telegramMessage,
        ]);

        if (! in_array($response->getStatusCode(), [200, 202])) {
            throw new CouldNotSendNotification($response->getStatusCode(), $response->getBody()->getContent());
        }

        event(new MessageWasSent($notifiable, $notification));
    }

    /**
     * @param  \Alexsoft\LaravelNotificationsTelegram\TelegramMessage  $message
     * @return string
     */
    protected function getText(TelegramMessage $message)
    {
        switch ($message->level) {
            case 'success':
                $word = "Hoooray! \xF0\x9F\x98\x8E";
                break;
            case 'error':
                $word = "Whoops! \xF0\x9F\x98\xB0";
                break;
            default:
                $word = "Hello! \xF0\x9F\x91\xBB";
                break;
        }

        $texts = [];

        $texts[] = '<b>'.$word.'</b>'."\n";

        if (count($message->introLines)) {
            $texts[] = implode("\n", $message->introLines);
        }

        if (isset($message->actionText)) {
            $texts[] = '<a href="'.$message->actionUrl.'">'.$message->actionText.'</a>';
        }

        if (count($message->outroLines)) {
            $texts[] = implode("\n", $message->outroLines);
        }

        $texts[] = "\nRegards,\n".config('app.name');

        return implode("\n", $texts);
    }

    /**
     * @return string
     */
    protected function getTelegramApiUrl()
    {
        return 'https://api.telegram.org/bot'.$this->telegramBotKey.'/sendMessage';
    }
}
