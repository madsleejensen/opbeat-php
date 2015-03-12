<?php namespace Opbeat\Log;

use JsonSerializable;
use Opbeat\Factory;
use OutOfBoundsException;

class Trace implements JsonSerializable
{
    use Factory;

    protected $trace;
    protected $contextLineCount;

    public function __construct(array $trace, $contextLineCount = 3)
    {
        $this->trace = $trace;
        $this->contextLineCount = $contextLineCount;
    }

    public function getFirstFrame()
    {
        return $this->getFrame(0);
    }

    public function getFrame($index)
    {
        $validFrames     = $this->validFrames();
        $validFrameCount = count($validFrames);

        if ($validFrameCount <= $index) {
            throw new OutOfBoundsException("Trace only contains {$validFrameCount}. Requested frame: {$index}");
        }

        return $this->mapFrame($validFrames[$index]);
    }

    protected function getFullFunctionName($frame)
    {
        switch ($frame['type']) {
            case '::':
            case '->':
                return $frame['class'] . $frame['type'] . $frame['function'];

            default:
                return $frame['function'];
        }
    }

    protected function isValidFrame($frame)
    {
        return isset($frame['file']);
    }

    protected function getLinesFromFile($file, $startLine, $endLine)
    {
        $fp = fopen($file, 'r');
        $currentLine = 0;
        $lines = [];

        while (($line = fgets($fp)) !== false) {
            $currentLine++;

            if ($currentLine < $startLine || $currentLine > $endLine) {
                continue;
            }

            $lines[$currentLine] = $line;
        }

        fclose($fp);

        return $lines;
    }

    protected function getContextForFrame($frame)
    {
        $context   = [];
        $lineCount = $this->contextLineCount;
        $startLine = $frame['line'] - $lineCount;
        $endLine   = $frame['line'] + $lineCount;

        $contextLines = $this->getLinesFromFile($frame['file'], $startLine, $endLine);

        if (!empty($contextLines)) {
            $context['pre_context']  = array_slice($contextLines, 0, $lineCount);
            $context['context_line'] = $contextLines[$frame['line']];
            $context['post_context'] = array_slice($contextLines, -$lineCount);
        }

        return $context;
    }

    protected function mapFrame($frame)
    {
        $values = [
            'abs_path' => $frame['file'],
            'filename' => basename($frame['file']),
            'function' => $this->getFullFunctionName($frame),
            'lineno' => $frame['line']
        ];

        $values = array_merge($values, $this->getContextForFrame($frame));

        if (!empty($frame['args'])) {
            $values['vars'] = $frame['args'];
        }

        return $values;
    }

    protected function validFrames()
    {
        return array_values(array_filter(
            $this->trace,
            [$this, 'isValidFrame']
        ));
    }

    public function jsonSerialize()
    {
        return array_map(
            [$this, 'mapFrame'],
            $this->validFrames()
        );
    }
}
