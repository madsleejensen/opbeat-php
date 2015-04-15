<?php namespace Opbeat\Log;

use ErrorException;
use Exception;
use JsonSerializable;
use Opbeat\Factory;
use OutOfBoundsException;

class Entry implements JsonSerializable
{
    use Factory;

    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_FATAL = 'fatal';

    protected $exception;
    protected $requestAttributes;
    protected $baseAttributes;

    public function __construct(Exception $exception, $requestAttributes = null)
    {
        $this->exception = $exception;
        $this->requestAttributes = $requestAttributes ?: $_SERVER;

        $this->setBaseAttributes();
    }

    public function getErrorLevels()
    {
        return [
            static::LEVEL_DEBUG,
            static::LEVEL_INFO,
            static::LEVEL_WARNING,
            static::LEVEL_ERROR,
            static::LEVEL_FATAL,
        ];
    }

    protected function setBaseAttributes()
    {
        $this->baseAttributes = [
            'timestamp' => date('c'),
            'level' => $this->getErrorLevel(),
            'logger' => 'opbeat-php'
        ];

        try {
            $this->baseAttributes['culprit'] = $this->getTrace()->getFirstFrame()['function'];
        } catch (OutOfBoundsException $e) {
            // do nothing
        }
    }

    protected function getTrace()
    {
        static $trace;
        if (!isset($trace)) {
            $trace = Trace::create($this->exception->getTrace());
        }

        return $trace;
    }

    protected function getErrorLevel()
    {
        if ($this->exception instanceof ErrorException) {
            switch ($this->exception->getSeverity()) {
                case E_USER_ERROR:
                case E_RECOVERABLE_ERROR:
                    return static::LEVEL_ERROR;

                case E_WARNING:
                case E_USER_WARNING:
                    return static::LEVEL_WARNING;

                case E_NOTICE:
                case E_USER_NOTICE:
                case E_DEPRECATED:
                case E_USER_DEPRECATED:
                    return static::LEVEL_INFO;
            }
        }

        return static::LEVEL_ERROR;
    }

    public function jsonSerialize()
    {
        return array_merge($this->baseAttributes, [
            'message' => get_class($this->exception).': '.$this->exception->getMessage(),
            'exception' => [
                'type' => get_class($this->exception),
                'value' => $this->exception->getMessage(),
            ],
            'stacktrace' => $this->getTrace(),
            'http' => Http::create($this->requestAttributes),
        ]);
    }
}
