<?php namespace Opbeat;

trait Factory
{
    public static function create(...$args)
    {
        return new static(...$args);
    }
}
