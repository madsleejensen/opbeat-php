<?php namespace Opbeat\Message\Part;

use Opbeat\Exception as OpbeatException;
use Opbeat\Message\Part\Interface as PartInterface;
use Opbeat\Utils;

class Http implements PartInterface
{
    private $_url;
    private $_method;
    private $_http_host;
    private $_data;
    private $_query_string;
    private $_cookies;
    private $_remote_host;
    private $_user_agent;
    private $_secure;
    private $_env;

    public function __construct()
    {
        $https = !empty($_SERVER['HTTPS']);
        $protocol = $https ? 'https://' : 'http://';

        // required parameters.
        $this->setUrl($protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])
             ->setMethod($_SERVER['REQUEST_METHOD']);
    }

    public function loadFromRequest()
    {
        $https = !empty($_SERVER['HTTPS']);
        $protocol = $https ? 'https://' : 'http://';

        $this->setSecure($https)
            ->setUrl($protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])
            ->setMethod($_SERVER['REQUEST_METHOD'])
            ->setData($_POST)
            ->setQueryString($_SERVER['QUERY_STRING'])
            ->setCookies($_COOKIE)
            ->setRemoteHost($_SERVER['HTTP_HOST'])
            ->setHttpHost($_SERVER['HTTP_HOST'])
            ->setUserAgent($_SERVER['HTTP_USER_AGENT'])
            ->setSecure($https)
            ->setEnv($_SERVER['SERVER_SOFTWARE'], $_SERVER['GATEWAY_INTERFACE']);
    }

    public function setCookies(Array $cookies)
    {
        $this->_cookies = Utils::inlineCookies($cookies);

        return $this;
    }

    public function setData(Array $data)
    {
        $this->_data = $data;

        return $this;
    }

    public function setEnv($server_software = null, $gateway_interface = null)
    {
        $this->_env = array(
            'SERVER_SOFTWARE' => $server_software,
            'GATEWAY_INTERFACE' => $gateway_interface,
        );

        return $this;
    }

    public function setHttpHost($http_host)
    {
        $this->_http_host = $http_host;

        return $this;
    }

    public function setQueryString($query_string)
    {
        $this->_query_string = $query_string;

        return $this;
    }

    public function setRemoteHost($remote_host)
    {
        $this->_remote_host = $remote_host;

        return $this;
    }

    public function setSecure($secure)
    {
        $this->_secure = (bool) $secure;

        return $this;
    }

    public function setUrl($url)
    {
        $this->_url = $url;

        return $this;
    }

    public function setMethod($method)
    {
        $valid_methods = array('GET', 'POST');
        if (!in_array($method, $valid_methods)) {
            throw new OpbeatException("Method: invalid value, valid values are (".implode(',', $valid_methods).")");
        }

        $this->_method = $method;

        return $this;
    }

    public function setUserAgent($user_agent)
    {
        $this->_user_agent = $user_agent;

        return $this;
    }

    public function build()
    {
        $result = array();
        $properties = array('url', 'method', 'data', 'query_string', 'cookies', 'remote_host', 'http_host', 'user_agent', 'secure', 'env');
        foreach ($properties as $property) {
            $value = $this->{"_".$property};
            if (!is_null($value)) {
                $result[$property] = $value;
            }
        }

        return $result;
    }
}
