<?php namespace spec\Opbeat\Log;

use Exception;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TraceSpec extends ObjectBehavior
{
    protected $excludedFrameCount = 1;
    protected $trace = array(
      array(
        'function' => 'invalid',
        'class' => 'ExcludeFromTrace',
        'type' => '->',
        'args' =>
        array(
        ),
      ),
      array(
        'file' => __DIR__ . '/../exceptionThrower.php',
        'line' => 12,
        'function' => 'throwWithMessage',
        'class' => 'ExceptionThrower',
        'type' => '::',
        'args' =>
        array(
          0 => 'Rotten banana',
        ),
      ),
      array(
        'file' => __DIR__ . '/../exceptionThrower.php',
        'line' => 25,
        'function' => 'eat',
        'class' => 'Banana',
        'type' => '->',
        'args' =>
        array(
        ),
      ),
    );

    public function let()
    {
        $this->beConstructedWith($this->trace);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Opbeat\Log\Trace');
    }

    public function it_returns_correct_trace_count()
    {
        $this->jsonSerialize()->shouldHaveCount(count($this->trace) - $this->excludedFrameCount);
    }

    public function it_returns_correct_trace_format()
    {
        $this->jsonSerialize()[0]->shouldHaveKey('abs_path');
        $this->jsonSerialize()[0]->shouldHaveKey('filename');
        $this->jsonSerialize()[0]->shouldHaveKey('lineno');
        $this->jsonSerialize()[0]->shouldHaveKey('function');
        $this->jsonSerialize()[0]->shouldHaveKey('vars');
        $this->jsonSerialize()[0]->shouldHaveKey('context_line');
        $this->jsonSerialize()[0]->shouldHaveKey('pre_context');
        $this->jsonSerialize()[0]->shouldHaveKey('post_context');
        $this->jsonSerialize()[0]['pre_context']->shouldHaveCount(3);
        $this->jsonSerialize()[0]['post_context']->shouldHaveCount(3);
        $this->jsonSerialize()[1]->shouldNotHaveKey('vars');
    }

    public function it_should_return_correct_frame()
    {
        $this->getFirstFrame()->shouldBeArray();
        $this->getFirstFrame()->shouldHaveKey('abs_path');
    }

    public function it_should_throw_error_when_requesting_frame_with_invalid_index()
    {
        $this->shouldThrow('\OutOfBoundsException')->duringGetFrame(1000);
    }
}
