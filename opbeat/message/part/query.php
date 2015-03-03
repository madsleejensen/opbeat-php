<?php namespace Opbeat\Message\Part;

use Opbeat\Message\Part\Interface as PartInterface;

class Query implements PartInterface
{
    private $_query;
    private $_engine;

    public function __construct($query, $engine = null)
    {
        $this->_query = $query;
        $this->_engine = $engine;
    }

    public function build()
    {
        return array(
            'query' => $this->_query,
            'engine' => $this->_engine,
        );
    }
}
