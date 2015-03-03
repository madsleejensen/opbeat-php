<?php namespace Opbeat;

use Opbeat\Message\Part\Exception as PartException;
use Opbeat\Message\Part\Stacktrace;
use Opbeat\Message\Part\Http as PartHttp;
use Opbeat\Message\Part\Query as PartQuery;
use Opbeat\Exception as OpbeatException;
use Opbeat\Message as Message;
use Opbeat\Message\Part\Interface as PartInterface;
use Opbeat\Utils;
use Exception;

class Message
{
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFOMATION = 'infomation';
    const LEVEL_ERROR = 'error';
    const LEVEL_WARNING = 'warning';
    const LEVEL_FATEL = 'fatal';

    private $_message;
    private $_timestamp;
    private $_level;
    private $_logger;
    private $_client_supplied_id;
    private $_culprit;
    private $_machine;
    private $_extra;
    private $_param_message;
    private $_exception;
    private $_stacktrace;
    private $_http;
    private $_user;
    private $_query;

    public function __construct($message, $level)
    {
        $this->setMessage($message);
        $this->setLevel($level);
    }

    public function setMessage($message)
    {
        $this->_message = $message;
    }

    public function setException(Exception $exception)
    {
        $this->_exception = new PartException($exception);
        $this->_stacktrace = Stacktrace::createByException($exception);

        return $this;
    }

    public function setStacktrace(Stacktrace $stacktrace)
    {
        $this->_stacktrace = $stacktrace;

        return $this;
    }

    public function setHTTP(PartHttp $http)
    {
        $this->_http = $http;

        return $this;
    }

    public function setQuery(PartQuery $query)
    {
        $this->_query = $query;

        return $this;
    }

    public function setClientSuppliedId($client_supplied_id)
    {
        $encoded = (string) $client_supplied_id;

        if (strlen($encoded) > 36) {
            throw new OpbeatException("Client supplied ID: can maximum be 36 characters long.");
        }

        $this->_client_supplied_id = $encoded;
    }

    public function setCulprit($culprit)
    {
        $this->_culprit = $culprit;
    }

    public function setExtra(Array $extra)
    {
        $this->_extra = $extra;
    }

    public function setLevel($level)
    {
        $valid_levels = array(Message::LEVEL_ERROR, Message::LEVEL_DEBUG, Message::LEVEL_FATEL, Message::LEVEL_INFOMATION, Message::LEVEL_WARNING);

        if (!in_array($level, $valid_levels)) {
            throw new OpbeatException("Level: invalid value, valid values are (".implode(',', $valid_levels).")");
        }

        $this->_level = $level;
    }

    public function setLogger($logger)
    {
        $this->_logger = $logger;
    }

    public function setMachine($machine_name)
    {
        $this->_machine = array(
            'hostname' => $machine_name,
        );
    }

    public function setParamMessage($message, Array $params)
    {
        $this->_param_message = array(
            'message' => $message,
            'params' => $params,
        );
    }

    public function setTimestamp($timestamp)
    {
        if (!strtotime($timestamp)) {
            throw new OpbeatException("Timestamp: should be a valid time string (ISO 8601)");
        }

        $this->_timestamp = $timestamp;
    }

    public function setUser(Array $user)
    {
        $this->_user = $user;
    }

    public function build()
    {
        $data = array();
        $data['message'] = $this->_message;
        $data['level'] = $this->_level;

        $properties = array('param_message', 'timestamp', 'logger', 'culprit', 'client_supplied_id', 'machine', 'exception', 'stacktrace', 'http', 'query', 'user', 'extra');
        foreach ($properties as $property_name) {
            $property_value = $this->{"_".$property_name};
            if (!is_null($property_value)) {
                $data[$property_name] = ($property_value instanceof PartInterface) ? $property_value->build() : $property_value;
            }
        }

        $data += $this->getDefaultValues();

        return json_encode($data);
    }

    private function getDefaultValues()
    {
        $default = array();
        $default['timestamp'] = date('c');
        $default['client_supplied_id'] = Utils::uniqueID();
        $default['machine'] = array(
            'hostname' => gethostname(),
        );

        if ($this->is_http_request()) {
            $http = new PartHttp;
            $http->loadFromRequest();

            $default['http'] = $http->build();
        }

        return $default;
    }

    private function is_http_request()
    {
        return !empty($_SERVER['HTTP_HOST']);
    }
}
