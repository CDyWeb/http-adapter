<?php

namespace cdyweb\http\guzzle;

use cdyweb\http\Adapter;
use cdyweb\http\BaseAdapter;
use cdyweb\http\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;


class Guzzle6 extends BaseAdapter {

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * @var object
     */
    protected $handler;

    /**
     * @var array
     */
    private $basicAuth = null;

    /**
     * @return \GuzzleHttp\Client
     */
    public function getClient() {
        if (empty($this->client)) {
            #$stack = new HandlerStack();
            #$stack->setHandler($this->getHandler());
            $opt=['verify'=>false]; //, 'handler'=>$stack];
            $this->client = new \GuzzleHttp\Client($opt);
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
     * @throws RequestException
     */
    public function send($request)
    {
        if (!empty($this->append_headers)) {
            foreach ($this->append_headers as $name=>$value) {
                $request = $request->withHeader($name, $value);
            }
        }

        $opt = [];
        if (!empty($this->basicAuth)) $opt['auth'] = $this->basicAuth;

        try {
            return $this->getClient()->send($request, $opt);
        } catch (\GuzzleHttp\Exception\RequestException $ex) {
            throw new RequestException($ex->getMessage(), $ex->getRequest(), $ex->getResponse(), $ex);
        }
    }

    public function setBasicAuth($user, $pass)
    {
        $this->basicAuth = [$user, $pass];
    }

    public function mock(array $responses) {
        $queue = [];
        foreach ($responses as $r) {
            $status     = isset($r[0]) ? $r[0] : 200;
            $headers    = isset($r[1]) ? $r[1] : [];
            $body       = isset($r[2]) ? $r[2] : null;
            $version    = isset($r[3]) ? $r[3] : '1.1';
            $reason     = isset($r[4]) ? $r[4] : null;
            $queue[] = new Response($status,$headers,$body,$version,$reason);
        }
        $this->handler = new MockHandler($queue);
        return $this->handler;
    }

    protected function getHandler() {
        if (empty($this->handler)) {
            $this->handler = new CurlHandler();
        }
        return $this->handler;
    }

}
