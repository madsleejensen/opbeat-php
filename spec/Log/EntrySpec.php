<?php namespace spec\Opbeat\Log;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Exception;

class EntrySpec extends ObjectBehavior
{
    protected $exception;

    function let()
    {
        $this->beConstructedWith($this->exception = new Exception('This is an exception'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Opbeat\Log\Entry');
    }

    function it_should_always_return_basic_attributes_from_jsonserialize()
    {
        $this->jsonSerialize()->shouldBeArray();
        $this->jsonSerialize()->shouldHaveKey('timestamp');
        $this->jsonSerialize()->shouldHaveKey('level');
        $this->jsonSerialize()->shouldHaveKey('culprit');
        $this->jsonSerialize()->shouldHaveKey('message');
        $this->jsonSerialize()->shouldHaveKey('exception');
        $this->jsonSerialize()->shouldHaveKey('stacktrace');
    }

    function it_should_always_include_exception_details()
    {
        $this->jsonSerialize()['exception']->shouldBeArray();
        $this->jsonSerialize()['exception']->shouldHaveKey('type');
        $this->jsonSerialize()['exception']->shouldHaveKey('value');
    }

    function it_returns_valid_error_level()
    {
        $this->getErrorLevels()->shouldContain($this->jsonSerialize()['level']);
    }
}
