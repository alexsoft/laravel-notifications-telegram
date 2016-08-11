<?php

namespace Alexsoft\LaravelNotificationsTelegram\Test;

use Alexsoft\LaravelNotificationsTelegram\TelegramMessage;
use PHPUnit_Framework_TestCase;

class MessageTest extends PHPUnit_Framework_TestCase
{
    /** @var \Alexsoft\LaravelNotificationsTelegram\TelegramMessage */
    protected $message;

    public function setUp()
    {
        parent::setUp();

        $this->message = new TelegramMessage();
    }

    /** @test */
    public function it_can_change_levels_to_success_and_error()
    {
        $this->message->error();

        $this->assertEquals('error', $this->message->level);

        $this->message->success();

        $this->assertEquals('success', $this->message->level);
    }
}