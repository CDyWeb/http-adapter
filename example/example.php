<?php

//load the classes
require 'vendor/autoload.php';

//create a http guzzle adapter, no matter which guzzle version is loaded
$adapter = \cdyweb\http\guzzle\Guzzle::getAdapter();

//GET some example REST data
$str = $adapter->get('http://jsonplaceholder.typicode.com/posts/1')->getBody();
$post = json_decode($str, true);
var_dump($post);

//PUT (update) the data
$post['title'] = 'test123';
$str = json_encode($post);
$response = $adapter->put('http://jsonplaceholder.typicode.com/posts/1', array(), $str);
var_dump($response->getReasonPhrase());

//POST (create) new data
unset($post['id']);
$str = json_encode($post);
$response = $adapter->post('http://jsonplaceholder.typicode.com/posts', array(), $str);
$str = $response->getBody()->getContents();
$json = json_decode($str,true);
var_dump($json);
$id = $json['id'];

//DELETE a record
$response = $adapter->delete('http://jsonplaceholder.typicode.com/posts/'.$id);
var_dump($response->getReasonPhrase());
