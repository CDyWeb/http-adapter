<?php

/**
 * Class cdyweb_http_Guzzle3Test
 * @property \cdyweb\http\guzzle\Guzzle3 $adapter
 */
class cdyweb_http_Guzzle3Test extends PHPUnit_Framework_TestCase {

    public function setUp() {
        $this->adapter = \cdyweb\http\guzzle\Guzzle::getAdapter();
    }

    public function test_getClient() {
        $this->assertInstanceOf('\cdyweb\http\guzzle\Guzzle3',$this->adapter);
        $client = $this->adapter->getClient();
        $this->assertInstanceOf('\Guzzle\Http\Client',$client);
    }

    public function test_get() {
        /**
         * @var \Guzzle\Plugin\Mock\MockPlugin $mock
         * @var \Guzzle\Http\Message\Request $request
         */
        $mock = $this->adapter->mock([[200],[200],[200]]);

        $this->adapter->get('http://a:b@example.com/?123');

        $arr = $mock->getReceivedRequests();
        $request = end($arr);
        $this->assertInstanceOf('\Guzzle\Http\Message\Request',$request);
        $this->assertEquals('http://example.com/?123', $request->getUrl());

        $this->adapter->appendRequestHeaders(['xxx'=>'yyy']);
        $this->adapter->get('http://www.example.com/?321');
        $arr = $mock->getReceivedRequests();
        $request = end($arr);
        $this->assertInstanceOf('\Guzzle\Http\Message\Request',$request);
        $this->assertEquals('http://www.example.com/?321', $request->getUrl());
        $this->assertEquals('yyy', $request->getHeader('xxx'));

        $this->adapter->appendRequestHeaders(null);
        $this->adapter->setBasicAuth('foo','bar');
        $this->adapter->get('http://www.example.com/?321');
        $arr = $mock->getReceivedRequests();
        $request = end($arr);
        $this->assertInstanceOf('\Guzzle\Http\Message\Request',$request);
        $this->assertEquals('http://www.example.com/?321', $request->getUrl());
        $this->assertEquals(null, $request->getHeader('xxx'));
        $this->assertEquals('Basic Zm9vOmJhcg==', $request->getHeader('Authorization'));
    }

    public function test_post() {
        /**
         * @var \Guzzle\Plugin\Mock\MockPlugin $mock
         * @var \Guzzle\Http\Message\EntityEnclosingRequest $request
         */
        $mock = $this->adapter->mock([[200]]);
        $result = $this->adapter->post('http://c:d@example.com/?a!', array(), array('aaa'=>'bbb','ccc'=>'ddd'));
        $this->assertInstanceOf('\Psr\Http\Message\ResponseInterface', $result);

        $arr = $mock->getReceivedRequests();
        $request = end($arr);
        $this->assertInstanceOf('\Guzzle\Http\Message\EntityEnclosingRequest',$request);
        $body = (string) $request->getBody();
        $this->assertEquals('aaa=bbb&ccc=ddd', $body);
        $this->assertEquals('application/x-www-form-urlencoded', $request->getHeader('Content-Type'));
    }

    public function test_methods() {
        $mock = $this->adapter->mock([[201],[202],[203],[204],[205]]);

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