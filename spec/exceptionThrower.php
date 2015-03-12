<?php

class Banana
{
    public static function create()
    {
        return new static;
    }

    public function eat()
    {
        ExceptionThrower::throwWithMessage('Rotten banana');
    }
}

class ExceptionThrower
{
    public static function throwWithMessage($message)
    {
        throw new Exception($message);
    }
}

try {
    Banana::create()->eat();
} catch (Exception $e) {
    var_export($e->getTrace());
}

// This file is included to allow Trace to read context and post/pre-context lines
