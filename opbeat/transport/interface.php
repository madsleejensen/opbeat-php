<?php

interface opbeat_transport_interface
{
    public function send(Opbeat_Message $message);
}
