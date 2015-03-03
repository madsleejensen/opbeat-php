<?php namespace Opbeat\Message\Part;

use Opbeat\Message\Part\Interface as PartInterface;

class Exception implements PartInterface
{
    private $_exception;

    public function __construct(Exception $exception)
    {
        $this->_exception = $exception;
    }

    public function build()
    {
        $data = array(
            'type' => get_class($this->_exception),
            'value' => $this->_exception->getMessage(),
        );

        return $data;
    }
}
