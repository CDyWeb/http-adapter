<?php

namespace cdyweb\http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface Adapter {

    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     */
    public function get($uri = null, array $headers = array(), $options = array());
    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     */
    public function head($uri = null, array $headers = array(), array $options = array());
    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     */
    public function delete($uri = null, array $headers = array(), $body = null, array $options = array());
    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     */
    public function put($uri = null, array $headers = array(), $body = null, array $options = array());
    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     */
    public function patch($uri = null, array $headers = array(), $body = null, array $options = array());
    /**
     * @param string $uri
     * @param array $headers
     * @param array $options
     * @return ResponseInterface
     */
    public function post($uri = null, array $headers = array(), $postBody = null, array $options = array());
    /**
     * @param string $uri
     * @param array $options
     * @return ResponseInterface
     */
    public function options($uri = null, array $options = array());

    /**
     * @param string $method
     * @param null $uri
     * @param null $headers
     * @param null $body
     * @param array $options
     * @return RequestInterface
     */
    public function createRequest($method = 'GET', $uri = null, array $headers = array(), $body = null, array $options = array());

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function send($request);

    /**
     * @param array $headers
     */
    public function appendRequestHeaders($headers);

    /**
     * @param array $header
     */
    public function appendRequestHeader($name, $value);

    /**
     * @return array
     */
    public function getRequestHeaders();

    /**
     * @return mixed
     */
    public function getClient();

    /**
     * @param $user
     * @param $pass
     */
    public function setBasicAuth($user, $pass);

    public function mock(array $responses);
}
