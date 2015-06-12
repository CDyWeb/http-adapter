<?php

namespace cdyweb\http\guzzle;

use cdyweb\http\BaseAdapter;
use cdyweb\http\psr\Request;
use cdyweb\http\psr\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Guzzle3 extends BaseAdapter
{

    /**
     * @var \Guzzle\Http\Client
     */
    protected $client;

    /**
     * @return \Guzzle\Http\Client
     */
    public function getClient() {
        if (empty($this->client)) {
            $this->client = new \Guzzle\Http\Client();
        }
        return $this->client;
    }

    /**
     * @param string $method
     * @param null $uri
     * @param null $headers
     * @param null $body
     * @param array $options
     * @return RequestInterface
     */
    public function createRequest($method = 'GET', $uri = null, array $headers = array(), $body = null, array $options = array())
    {
        return new Request($method, $uri, $headers, $body);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function send($request)
    {
        /**
         * var Guzzle\Http\Message\Response $response
         */
        $headers = $request->getHeaders();
        if (!empty($this->append_headers)) $headers = array_merge($headers, $this->append_headers);

        $response = $this->getClient()->createRequest(
            $request->getMethod(),
            $request->getUri(),
            $headers,
            $request->getBody()
        )->send();

        return new Response($response->getStatusCode(), $response->getHeaderLines(), $response->getBody());
    }


    public function mock(array $responses) {
        $plugin = new MockPlugin();
        foreach ($responses as $r) {
            $status     = isset($r[0]) ? $r[0] : 200;
            $headers    = isset($r[1]) ? $r[1] : [];
            $body       = isset($r[2]) ? $r[2] : null;
            $mockResponse = new \Guzzle\Http\Message\Response($status, $headers, $body);
            $plugin->addResponse($mockResponse);
        }
        $this->getClient()->addSubscriber($plugin);
        return $plugin;
    }
}
