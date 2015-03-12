<?php namespace spec\Opbeat;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Illuminate\Contracts\Config\Repository as Config;
use Exception;

class ClientSpec extends ObjectBehavior
{
    function let(Config $config)
    {
        $config->get('opbeat.organization_id')->willReturn('...');
        $config->get('opbeat.app_id')->willReturn('...');
        $config->get('opbeat.access_token')->willReturn('...');
        $config->get('opbeat.enable_exception_handler', true)->willReturn(true);
        $config->get('opbeat.enable_error_handler', true)->willReturn(true);

        $this->beConstructedWith($config);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Opbeat\Client');
    }

    function it_should_register_exception_handler()
    {
        $expectedHandler = [$this->getWrappedObject(), 'catchException'];
        $this->exceptionHandler()->shouldReturn(set_exception_handler(null));
    }

    function it_should_not_register_exception_handler_when_passed_false(Config $config)
    {
        $config->get('opbeat.enable_exception_handler', true)->willReturn(false);
        $this->beConstructedWith($config);

        $expectedHandler = [$this->getWrappedObject(), 'catchException'];
        $this->exceptionHandler()->shouldNotReturn(set_exception_handler(null));
    }

    function it_should_set_error_handler(Config $config)
    {
        $errorHandler = [$this->getWrappedObject(), ['handleError']];
        $this->errorHandler()->shouldReturn(set_error_handler(null));
    }

    function it_should_not_set_error_handler_when_passed_false(Config $config)
    {
        $config->get('opbeat.enable_error_handler', true)->willReturn(false);
        $this->beConstructedWith($config);

        $errorHandler = [$this->getWrappedObject(), ['handleError']];
        $this->errorHandler()->shouldNotReturn(set_error_handler(null));
    }
}
