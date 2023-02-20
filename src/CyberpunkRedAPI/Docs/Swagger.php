<?php

namespace CyberpunkRedAPI\Docs;

use CyberpunkRedAPI\AbstractRoute;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;

class Swagger extends AbstractRoute
{

    /**
     * @inheritDoc
     */
    public function __invoke(ServerRequestInterface $request, Response $response, array $args = []): Response
    {
        $format = $request->getAttribute('format', 'json');

        $swagger = \OpenApi\Generator::scan([__DIR__ . '/../../../config']);
        $outArray = $swagger->jsonSerialize();
        return $this->outputResponse($response, $outArray, $format);
    }
}