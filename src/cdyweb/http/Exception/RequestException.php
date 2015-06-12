<?php

namespace cdyweb\http\Exception;

class RequestException extends \RuntimeException {
    /** @var \Psr\Http\Message\RequestInterface */
    private $request;

    /** @var \Psr\Http\Message\ResponseInterface */
    private $response;

    public function __construct(
        $message,
        \Psr\Http\Message\RequestInterface $request,
        \Psr\Http\Message\ResponseInterface $response = null,
        \Exception $previous = null
    ) {
        $code = $response
            ? $response->getStatusCode()
            : 0;
        parent::__construct($message, $code, $previous);
        $this->request = $request;
        $this->response = $response;
    }

}
