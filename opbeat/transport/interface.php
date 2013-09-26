<?php

interface Opbeat_Transport_Interface {
	public function send(Opbeat_Message $message);
}