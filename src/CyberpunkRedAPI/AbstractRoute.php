<?php

namespace CyberpunkRedAPI;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Yaml\Yaml;

/**
 * Class AbstractRoute
 * @package ScionAPI
 */
abstract class AbstractRoute
{
    /**
     * @var array
     */
    protected $help = [];

    /**
     * @return object
     */
    public function getHelp()
    {
        return (object) $this->help;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    abstract public function __invoke(
        ServerRequestInterface $request,
        Response $response,
        array $args = []
    ): Response;

    /**
     * @param Response $response
     * @param array|object $outArray
     * @param string $format
     * @return Response
     */
    protected function outputResponse(Response $response, array|object $outArray, string $format = 'json') : Response
    {
        switch ($format) {
            case 'yaml':
            $contentType = 'application/yaml';
            $output = Yaml::dump($outArray, 10, 2, Yaml::DUMP_OBJECT_AS_MAP);
            break;
        default:
            $contentType = 'application/json';
            $output = json_encode($outArray, JSON_PRETTY_PRINT);
            break;
    }
        $response->getBody()->write($output);
        return $response
            ->withHeader('Content-Type', $contentType)
            ->withStatus(200);
    }
}
