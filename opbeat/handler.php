<?php

class Opbeat_Handler {
	private $_clients;

	public function __construct() {
		$this->_clients = array();
	}

	public function addClient(Opbeat_Client $client) {
		$this->_clients[] = $client;
	}

	public function removeClient(Opbeat_Client $client) {
		$index = array_search($client, $this->_clients);
		if ($index !== false) {
			array_splice($this->_clients, $index, 1);
		}
	}

	public function registerExceptionHandler() {
		set_exception_handler(array($this, 'handleException'));
	}

	public function registerErrorHandler() {
		set_error_handler(array($this, 'handleError'));
	}

	public function handleException(Exception $exception) {
		if ($exception instanceof Opbeat_Exception) {
			return; // exclude opbeat specific exceptions to avoid endless loops.
		}

		foreach ($this->_clients as $client) {
			$client->captureException($exception);
		}
	}

	public function handleError($code, $message, $file, $line, $context = null) {
		$message = $this->translateErrorCodeToReadable($code) . ':' . $message;
		$exception = new ErrorException($message, $code, 1, $file, $line);
		$level = $this->translateErrorCodeToLevel($code);

		foreach ($this->_clients as $client) {
			$client->captureException($exception, $level);
		}
	}

	private function translateErrorCodeToReadable($code) {
		switch ($code) {
			case E_ERROR: return 'E_ERROR';
			case E_WARNING: return 'E_WARNING';
			case E_PARSE: return 'E_PARSE';
			case E_NOTICE: return 'E_NOTICE';
			case E_CORE_ERROR: return 'E_CORE_ERROR';
			case E_CORE_WARNING: return 'E_CORE_WARNING';
			case E_COMPILE_ERROR: return 'E_COMPILE_ERROR';
			case E_COMPILE_WARNING: return 'E_COMPILE_WARNING';
			case E_USER_ERROR: return 'E_USER_ERROR';
			case E_USER_WARNING: return 'E_USER_WARNING';
			case E_USER_NOTICE: return 'E_USER_NOTICE';
			case E_STRICT: return 'E_STRICT';
			case E_RECOVERABLE_ERROR: return 'E_RECOVERABLE_ERROR';
		}

		if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
			switch ($code) {
				case E_DEPRECATED: return 'E_DEPRECATED';
				case E_USER_DEPRECATED: return 'E_USER_DEPRECATED';
			}
		}

		return 'E_UNKNOWN';
	}

	private function translateErrorCodeToLevel($code) {
		switch ($code) {
			case E_ERROR: return Opbeat_Message::LEVEL_ERROR;
			case E_WARNING: return Opbeat_Message::LEVEL_WARNING;
			case E_PARSE: return Opbeat_Message::LEVEL_ERROR;
			case E_NOTICE: return Opbeat_Message::LEVEL_INFORMATION;
			case E_CORE_ERROR: return Opbeat_Message::LEVEL_ERROR;
			case E_CORE_WARNING: return Opbeat_Message::LEVEL_WARNING;
			case E_COMPILE_ERROR: return Opbeat_Message::LEVEL_ERROR;
			case E_COMPILE_WARNING: return Opbeat_Message::LEVEL_WARNING;
			case E_USER_ERROR: return Opbeat_Message::LEVEL_ERROR;
			case E_USER_WARNING: return Opbeat_Message::LEVEL_WARNING;
			case E_USER_NOTICE: return Opbeat_Message::LEVEL_INFORMATION;
			case E_STRICT: return Opbeat_Message::LEVEL_INFORMATION;
			case E_RECOVERABLE_ERROR: return Opbeat_Message::LEVEL_ERROR;
		}

		if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
			switch ($code) {
				case E_DEPRECATED: return Opbeat_Message::LEVEL_WARNING;
				case E_USER_DEPRECATED: return Opbeat_Message::LEVEL_WARNING;
			}
		}

		return Opbeat_Message::LEVEL_ERROR;
	}
}