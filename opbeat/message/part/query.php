<?php

class Opbeat_Message_Part_Query implements Opbeat_Message_Part_Interface {

	private $_query;
	private $_engine;

	public function __construct($query, $engine = null) {
		$this->_query = $query;
		$this->_engine = $engine;
	}

	public function build() {
		return array(
			'query' => $this->_query,
			'engine' => $this->_engine
		);
	}
}