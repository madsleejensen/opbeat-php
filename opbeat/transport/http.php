<?php

class Opbeat_Transport_Http extends Opbeat_Transport implements Opbeat_Transport_Interface {
	public function send(Opbeat_Message $message) {
		$headers = $this->createHeaders();
		$endpoint = $this->_base_path . 'organizations/' . $this->_client->getOrganizationID() . '/apps/' . $this->_client->getApplicationID() . '/errors/';

		$handler = curl_init();
		curl_setopt($handler, CURLOPT_URL, $endpoint);
		curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($handler, CURLOPT_POST, true);
		curl_setopt($handler, CURLOPT_POSTFIELDS, $message->build());

		if ($this->isHTTPS()) {
			curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
		}

		curl_exec($handler);

		$response_status_code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
		if ($response_status_code != 202) {
			throw new Opbeat_Exception('Client: unable to send message');
		}
	}

	private function isHTTPS() {
		return stripos($this->_base_path, 'https://') !== false;
	}

	private function createHeaders() {
		return array(
			'Authorization: Bearer ' . $this->_client->getSecretToken(),
			'Content-Type: application/json',
			'User-Agent: opbeat/1.0'
		);
	}
}
