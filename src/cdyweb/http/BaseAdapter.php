<?php

namespace cdyweb\http;

use Psr\Http\Message\ResponseInterface;

abstract class BaseAdapter implements Adapter {
    /**
     * @var array
     */
    protected $append_headers = array();

    /**
     * @param array $headers
     */
    public function appendRequestHeaders($headers)
    {
        if (is_array($headers)) {
            $this->append_headers = $headers;
        } else {
            $this->append_headers = null;
        }
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     */
    public function get($uri = null, array $headers = array(), $options = array())
    {
        return $this->send($this->createRequest('GET', $uri, $headers, null, $options));
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     */
    public function head($uri = null, array $headers = array(), array $options = array())
    {
        return $this->send($this->createRequest('HEAD', $uri, $headers, null, $options));
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     */
    public function delete($uri = null, array $headers = array(), $body = null, array $options = array())
    {
        return $this->send($this->createRequest('DELETE', $uri, $headers, $body, $options));
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     */
    public function put($uri = null, array $headers = array(), $body = null, array $options = array())
    {
        return $this->send($this->createRequest('PUT', $uri, $headers, $body, $options));
    }

    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     */
    public function patch($uri = null, array $headers = array(), $body = null, array $options = array())
    {
        return $this->send($this->createRequest('PATCH', $uri, $headers, $body, $options));
    }

    /**
     * @param null $uri
     * @param array $headers
     * @param mixed $postBody
     * @param array $options
     * @return ResponseInterface
     */
    public function post($uri = null, array $headers = array(), $postBody = null, array $options = array())
    {
        if (is_array($postBody)) {
            $postBody = http_build_query($postBody);
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }
        return $this->send($this->createRequest('POST', $uri, $headers, $postBody, $options));
    }

    /**
     * @param string $uri
     * @param array $options
     * @return ResponseInterface
     */
    public function options($uri = null, array $options = array())
    {
        return $this->send($this->createRequest('OPTIONS', $uri, array(), null, $options));
    }

}