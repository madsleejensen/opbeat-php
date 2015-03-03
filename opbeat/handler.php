<?php namespace Opbeat;

use Opbeat\Client;
use Opbeat\Exception as OpbeatException;
use Opbeat\Message as Message;
use ErrorException;
use Exception;

class Handler
{
    private $_clients;

    public function __construct()
    {
        $this->_clients = array();
    }

    public function addClient(Client $client)
    {
        $this->_clients[] = $client;
    }

    public function removeClient(Client $client)
    {
        $index = array_search($client, $this->_clients);
        if ($index !== false) {
            array_splice($this->_clients, $index, 1);
        }
    }

    public function registerExceptionHandler()
    {
        set_exception_handler(array($this, 'handleException'));
    }

    public function registerErrorHandler()
    {
        set_error_handler(array($this, 'handleError'));
    }

    public function handleException(Exception $exception)
    {
        if ($exception instanceof OpbeatException) {
            return; // exclude opbeat specific exceptions to avoid endless loops.
        }

        foreach ($this->_clients as $client) {
            $client->captureException($exception);
        }
    }

    public function handleError($code, $message, $file, $line, $context = null)
    {
        $message = $this->translateErrorCodeToReadable($code).':'.$message;
        $exception = new ErrorException($message, $code, 1, $file, $line);
        $level = $this->translateErrorCodeToLevel($code);

        foreach ($this->_clients as $client) {
            $client->captureException($exception, $level);
        }
    }

    private function translateErrorCodeToReadable($code)
    {
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

    private function translateErrorCodeToLevel($code)
    {
        switch ($code) {
            case E_ERROR: return Message::LEVEL_ERROR;
            case E_WARNING: return Message::LEVEL_WARNING;
            case E_PARSE: return Message::LEVEL_ERROR;
            case E_NOTICE: return Message::LEVEL_INFOMATION;
            case E_CORE_ERROR: return Message::LEVEL_ERROR;
            case E_CORE_WARNING: return Message::LEVEL_WARNING;
            case E_COMPILE_ERROR: return Message::LEVEL_ERROR;
            case E_COMPILE_WARNING: return Message::LEVEL_WARNING;
            case E_USER_ERROR: return Message::LEVEL_ERROR;
            case E_USER_WARNING: return Message::LEVEL_WARNING;
            case E_USER_NOTICE: return Message::LEVEL_INFOMATION;
            case E_STRICT: return Message::LEVEL_INFOMATION;
            case E_RECOVERABLE_ERROR: return Message::LEVEL_ERROR;
        }

        if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
            switch ($code) {
                case E_DEPRECATED: return Message::LEVEL_WARNING;
                case E_USER_DEPRECATED: return Message::LEVEL_WARNING;
            }
        }

        return Message::LEVEL_ERROR;
    }
}
