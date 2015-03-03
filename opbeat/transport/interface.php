<?php namespace Opbeat\Transport;

use Opbeat\Message as Message;

interface Interface
{
    public function send(Message $message);
}
