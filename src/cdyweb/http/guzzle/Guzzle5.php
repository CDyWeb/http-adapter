<?php

namespace cdyweb\http\guzzle;

use cdyweb\http\BaseAdapter;
use cdyweb\http\Exception\RequestException;
use cdyweb\http\psr\Request;
use cdyweb\http\psr\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Subscriber\History;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Guzzle5 extends BaseAdapter
{

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var array
     */
    private $basicAuth = null;

    /**
     * @return \GuzzleHttp\Client
     */
    public function getClient() {
        if (empty($this->client)) {
            $this->client = new \GuzzleHttp\Client();
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
         * var \GuzzleHttp\Message\Response $response
         */
        $headers = $request->getHeaders();
        if (!empty($this->append_headers)) $headers = array_merge($headers, $this->append_headers);

        $opt = [];
        if (!empty($this->basicAuth)) $opt['auth'] = $this->basicAuth;
        if (!empty($headers)) $opt['headers'] = $headers;
        $body = $request->getBody();
        if ($body !== null) $opt['body'] = $body;

        $g5request = $this->getClient()->createRequest(
            $request->getMethod(),
            $request->getUri(),
            $opt
        );

        try {
            $response = $this->getClient()->send($g5request);
            return new Response($response->getStatusCode(), $response->getHeaders(), $response->getBody());
        } catch (\GuzzleHttp\Exception\RequestException $ex) {
            $ex_request = $ex->getRequest();
            $ex_response = $ex->getResponse();
            throw new RequestException(
                $ex->getMessage(),
                $ex_request?new Request($ex_request->getMethod(), $ex_request->getUrl(), $ex_request->getHeaders(), $ex_request->getBody()) : null,
                $ex_response?new Response($ex_response->getStatusCode(), $ex_response->getHeaders(), $ex_response->getBody()) : null,
                $ex
            );
        }
    }

    public function setBasicAuth($user, $pass)
    {
        $this->basicAuth = [$user, $pass];
    }

    public function mock(array $responses) {
        $arr = [];
        foreach ($responses as $r) {
            $status     = isset($r[0]) ? $r[0] : 200;
            $headers    = isset($r[1]) ? $r[1] : [];
            $body       = isset($r[2]) ? $r[2] : null;
            $mockResponse = new \GuzzleHttp\Message\Response($status, $headers, $body);
            $arr[] = $mockResponse;
        }
        $mock = new Mock($arr);
        $history = new History();

        $this->getClient()->getEmitter()->attach($mock);
        $this->getClient()->getEmitter()->attach($history);

        return $history;
    }
}
