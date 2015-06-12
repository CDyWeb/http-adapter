<?php

namespace cdyweb\http\guzzle;

use cdyweb\http\BaseAdapter;
use cdyweb\http\Exception\RequestException;
use cdyweb\http\psr\Request;
use cdyweb\http\psr\Response;
use Guzzle\Plugin\Mock\MockPlugin;
use Guzzle\Plugin\CurlAuth\CurlAuthPlugin;
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

        try {
            $g3request = $this->getClient()->createRequest(
                $request->getMethod(),
                $request->getUri(),
                $headers,
                $request->getBody()
            );
            $response = $g3request->send();
        } catch (\Guzzle\Http\Exception\BadResponseException $ex) {
            $ex_request = $ex->getRequest();
            $ex_response = $ex->getResponse();
            throw new RequestException(
                $ex->getMessage(),
                $ex_request?new Request($ex_request->getMethod(), $ex_request->getUrl(), $ex_request->getHeaders()->toArray(), $request->getBody()) : null,
                $ex_response?new Response($ex_response->getStatusCode(), $ex_response->getHeaders()->toArray(), $ex_response->getBody()) : null,
                $ex
            );
        } catch (\Guzzle\Http\Exception\RequestException $ex) {
            $ex_request = $ex->getRequest();
            throw new RequestException(
                $ex->getMessage(),
                $ex_request?new Request($ex_request->getMethod(), $ex_request->getUrl(), $ex_request->getHeaders()->toArray(), $request->getBody()) : null,
                null,
                $ex
            );
        }

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

    public function setBasicAuth($user, $pass) {
        $this->getClient()->addSubscriber(new CurlAuthPlugin($user, $pass));
    }
}
