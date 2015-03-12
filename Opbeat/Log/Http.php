<?php namespace Opbeat\Log;

use InvalidArgumentException;
use JsonSerializable;
use Opbeat\Factory;

class Http implements JsonSerializable
{
    use Factory;

    protected $requestAttribute;
    protected $environmentAttributes;

    public function __construct($requestAttribute, $environmentAttributes = false)
    {
        $this->requestAttribute      = $requestAttribute;
        $this->environmentAttributes = $environmentAttributes;
    }

    protected function getProtocol()
    {
        if (($sp = $this->attributeValueOrFalse('SERVER_PROTOCOL')) === false) {
            return null;
        }

        return strtolower(substr($sp, 0, strpos($sp, '/')));
    }

    protected function urlOrigin($use_forwarded_host = false)
    {
        $s = $this->requestAttribute;
        $ssl = !empty($s['HTTPS']) && $s['HTTPS'] == 'on' ? true : false;
        $protocol = $this->getProtocol().($ssl ? 's' : '');
        $port = $s['SERVER_PORT'];
        $port = (!$ssl && $port == '80') || ($ssl && $port == '443') ? '' : ':'.$port;
        $host = $use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])
                  ? $s['HTTP_X_FORWARDED_HOST']
                  : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
        $host = isset($host) ? $host : $s['SERVER_NAME'].$port;
        return $protocol . '://' . $host;
    }

    protected function fullUrl($use_forwarded_host = false)
    {
        return $this->urlOrigin($this->requestAttribute, $use_forwarded_host)
               .$this->requestAttribute['REQUEST_URI'];
    }

    protected function isHttpRequest()
    {
        return $this->getProtocol() == 'http';
    }

    protected function attributeValueOrFalse($key)
    {
        if (!isset($this->requestAttribute[$key])) {
            return false;
        }
        return $this->requestAttribute[$key];
    }

    protected function getHeaders()
    {
        $headers = [];
        foreach ($this->requestAttribute as $key => $value) {
            if (substr($key, 0, 5) == 'HTTP_') {
                $headerName = strtolower(substr($key, 5));
                $headerName = implode('-', array_map('ucfirst', explode('_', $headerName)));

                $headers[$headerName] = $value;
            }
        }
        return $headers;
    }

    public function jsonSerialize()
    {
        if (!$this->isHttpRequest()) return null;

        return array_filter([
            'url' => $this->fullUrl(),
            'method' => $this->attributeValueOrFalse('REQUEST_METHOD'),
            'query_string' => $this->attributeValueOrFalse('QUERY_STRING'),
            'cookies' => $this->attributeValueOrFalse('HTTP_COOKIE'),
            'headers' => $this->getHeaders(),
            'remote_host' => $this->attributeValueOrFalse('REMOTE_ADDR'),
            'http_host' => $this->attributeValueOrFalse('HTTP_HOST'),
            'user_agent' => $this->attributeValueOrFalse('HTTP_USER_AGENT'),
            'secure' => ($this->attributeValueOrFalse('HTTPS') == 'on'),
            'env' => $this->environmentAttributes
        ]);
    }
}
