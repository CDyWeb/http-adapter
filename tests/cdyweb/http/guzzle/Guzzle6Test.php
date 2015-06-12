<?php

/**
 * Class cdyweb_http_Guzzle3Test
 * @property \cdyweb\http\guzzle\Guzzle6 $adapter
 */
class cdyweb_http_Guzzle6Test extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->adapter = \cdyweb\http\guzzle\Guzzle::getAdapter();
    }

    public function test_getClient() {
        $this->assertInstanceOf('\cdyweb\http\guzzle\Guzzle6',$this->adapter);
        $client = $this->adapter->getClient();
        $this->assertInstanceOf('\GuzzleHttp\Client',$client);
    }

    public function test_get() {
        /**
         * @var \GuzzleHttp\Handler\MockHandler $mock
         * @var \Psr\Http\Message\RequestInterface $request
         */
        $mock = $this->adapter->mock([[200],[200],[200]]);

        $this->adapter->get('http://a:b@example.com/?123');

        $request = $mock->getLastRequest();
        $this->assertInstanceOf('\Psr\Http\Message\RequestInterface',$request);
        $this->assertEquals('http://a:b@example.com/?123', $request->getUri());

        $this->adapter->appendRequestHeaders(['xxx'=>'yyy']);
        $this->adapter->get('http://www.example.com/?321');
        $request = $mock->getLastRequest();
        $this->assertInstanceOf('\Psr\Http\Message\RequestInterface',$request);
        $this->assertEquals('http://www.example.com/?321', $request->getUri());
        $this->assertEquals('yyy', $request->getHeaderLine('xxx'));

        $this->adapter->appendRequestHeaders(null);
        $this->adapter->setBasicAuth('foo','bar');
        $this->adapter->get('http://www.example.com/?321');
        $request = $mock->getLastRequest();
        $this->assertInstanceOf('\Psr\Http\Message\RequestInterface',$request);
        $this->assertEquals('http://www.example.com/?321', $request->getUri());
        $this->assertEquals(null, $request->getHeaderLine('xxx'));
        $this->assertEquals('Basic Zm9vOmJhcg==', $request->getHeaderLine('Authorization'));
    }

    public function test_post() {
        /**
         * @var \GuzzleHttp\Handler\MockHandler $mock
         * @var \Psr\Http\Message\RequestInterface $request
         */
        $mock = $this->adapter->mock([[200]]);
        $result = $this->adapter->post('http://c:d@example.com/?a!', array(), array('aaa'=>'bbb','ccc'=>'ddd'));
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $result);

        $request = $mock->getLastRequest();
        $this->assertInstanceOf('\Psr\Http\Message\RequestInterface',$request);
        $body = (string) $request->getBody();
        $this->assertEquals('aaa=bbb&ccc=ddd', $body);
        $this->assertEquals('application/x-www-form-urlencoded', $request->getHeaderLine('Content-Type'));
    }

    public function test_methods() {
        $mock = $this->adapter->mock([[200],[201],[202],[203],[204],[205]]);

        $result = $this->adapter->post('http://c:d@example.com/?a!');
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $result);
        $this->assertEquals(200, $result->getStatusCode());

        $result = $this->adapter->put('http://c:d@example.com/?a!');
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $result);
        $this->assertEquals(201, $result->getStatusCode());

        $result = $this->adapter->delete('http://c:d@example.com/?a!');
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $result);
        $this->assertEquals(202, $result->getStatusCode());

        $result = $this->adapter->head('http://c:d@example.com/?a!');
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $result);
        $this->assertEquals(203, $result->getStatusCode());

        $result = $this->adapter->patch('http://c:d@example.com/?a!');
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $result);
        $this->assertEquals(204, $result->getStatusCode());

        $result = $this->adapter->options('http://c:d@example.com/?a!');
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $result);
        $this->assertEquals(205, $result->getStatusCode());
    }

}