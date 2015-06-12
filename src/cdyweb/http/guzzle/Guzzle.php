<?php

namespace cdyweb\http\guzzle;

class Guzzle {
    private function __construct() {}

    /**
     * @return \cdyweb\http\Adapter
     */
    public static function getAdapter($version=null) {
        if (!$version) {
            if (class_exists('\Guzzle\Http\Client')) $version = '3';
            if (class_exists('\GuzzleHttp\Message\Request')) $version = '4';
            if (class_exists('\GuzzleHttp\Utils')) $version = '5';
            if (class_exists('\GuzzleHttp\Psr7\Request')) $version = '6';
        }
        if (!$version) {
            throw new \RuntimeException('Cannot determine your Guzzle version');
        }
        $class = "\\cdyweb\\http\\guzzle\\Guzzle{$version}";
        return new $class();
    }
}
