<?php

// Post error details asynchronously by running cURL through exec
class Opbeat_Transport_CurlCmd extends Opbeat_Transport implements Opbeat_Transport_Interface {
    public function send(Opbeat_Message $message) {
        $endpoint = $this->_base_path . 'organizations/' . $this->_client->getOrganizationID() . '/apps/' . $this->_client->getApplicationID() . '/errors/';

        $cmd  = 'curl -X POST ';
        $cmd .= implode(' ', $this->createHeaderArguments());
        $cmd .= " -d '" . $message->build() . "' " . $endpoint;
        $cmd .= " > /dev/null 2>&1 &";

        exec($cmd);
    }

    private function isHTTPS() {
        return stripos($this->_base_path, 'https://') !== false;
    }

    private function createHeaderArguments() {
        return array_map(function($header){
            return "-H '" . $header . "'";
        }, [
            'Authorization: Bearer ' . $this->_client->getSecretToken(),
            'Content-Type: application/json',
            'User-Agent: opbeat/1.0'
        ]);
    }
}
