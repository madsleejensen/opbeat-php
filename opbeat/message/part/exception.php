<?php

class opbeat_message_part_exception implements Opbeat_Message_Part_Interface
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
