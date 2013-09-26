<?php

class Opbeat_Client {
	private $_organization_id;
	private $_application_id;
	private $_secretToken;
	private $_transport;

	public function __construct($organization_id, $application_id, $secret_token, Opbeat_Transport_Interface $transport = null) {
		$this->_organization_id = $organization_id;
		$this->_application_id = $application_id;
		$this->_secretToken = $secret_token;

		if (is_null($transport)) {
			$this->_transport = new Opbeat_Transport_Http($this, 'https://opbeat.com/api/v1/'); // default to http.
		}
		else {
			$this->_transport = $transport;
		}
	}

	public function getOrganizationID() {
		return $this->_organization_id;
	}

	public function getApplicationID() {
		return $this->_application_id;
	}

	public function getSecretToken() {
		return $this->_secretToken;
	}

	public function captureMessage($message, $level = Opbeat_Message::LEVEL_ERROR) {
		$message = new Opbeat_Message($message, $level);
		$this->send($message);
	}

	public function captureException(Exception $exception, $level = Opbeat_Message::LEVEL_ERROR) {
		$message_text = get_class($exception) . ': ' . $exception->getMessage();
		$message = new Opbeat_Message($message_text, $level);
		$message->setException($exception);
		$this->send($message);
	}

	public function captureQuery($query, $engine = null, $level = Opbeat_Message::LEVEL_ERROR) {
		$message = new Opbeat_Message($query, $level);
		$message->setQuery(new Opbeat_Message_Part_Query($query, $engine));
		$this->send($message);
	}

	public function send(Opbeat_Message $message) {
		$this->_transport->send($message);
	}
}
