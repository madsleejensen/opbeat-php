<?php

class Opbeat_Transport {
    protected $_base_path;
    protected $_client;

    public function __construct(Opbeat_Client $client, $base_path) {
        $this->_base_path = $base_path;
        $this->_client = $client;
    }
}
