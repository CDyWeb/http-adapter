<?php

/**
 * Class cdyweb_http_Guzzle3Test
 * @property \cdyweb\http\guzzle\Guzzle5 $adapter
 */
class cdyweb_http_Guzzle5Test extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->adapter = new \cdyweb\http\guzzle\Guzzle5();
    }

    public function test_getClient() {
        $client = $this->adapter->getClient();
        $this->assertInstanceOf('\GuzzleHttp\Client',$client);
    }

    public function test_get() {
        /**
         * @var GuzzleHttp\Subscriber\History $mock
         * @var \GuzzleHttp\Message\Request $request
         */
        $mock = $this->adapter->mock([[200],[200],[200]]);

        $this->adapter->get('http://a:b@example.com/?123');

        $request = $mock->getLastRequest();
        $this->assertInstanceOf('\GuzzleHttp\Message\Request',$request);
        $this->assertEquals('http://a:b@example.com/?123', $request->getUrl());

        $this->adapter->appendRequestHeaders(['xxx'=>'yyy']);
        $this->adapter->get('http://www.example.com/?321');
        $request = $mock->getLastRequest();
        $this->assertInstanceOf('\GuzzleHttp\Message\Request',$request);
        $this->assertEquals('http://www.example.com/?321', $request->getUrl());
        $this->assertEquals('yyy', $request->getHeader('xxx'));

        $this->adapter->appendRequestHeaders(null);
        $this->adapter->setBasicAuth('foo','bar');
        $this->adapter->get('http://www.example.com/?321');
        $request = $mock->getLastRequest();
        $this->assertInstanceOf('\GuzzleHttp\Message\Request',$request);
        $this->assertEquals('http://www.example.com/?321', $request->getUrl());
        $this->assertEquals(null, $request->getHeader('xxx'));
        $this->assertEquals('Basic Zm9vOmJhcg==', $request->getHeader('Authorization'));
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