<?php

namespace CyberpunkRedAPI\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class FormatInjector implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // Which formats are allowed should be handled by the individual route, not the
        // Middleware, which has no awareness of readiness.
        $format = $request->getHeaderLine('format');
        //$format = $request->getHeader('format');
        // @TODO: We should really sanitize this, TBH.
        if (!$format) {
            $format = 'json';
        }

        //die(print_r($format,true));

        // enforce lowercase
        $format = strtolower($format);

        // add the format to the request attributes
        $request = $request->withAttribute('format', $format);

        return $handler->handle($request);
    }
}