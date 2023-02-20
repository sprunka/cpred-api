<?php

namespace CyberpunkRedAPI\Choose;

use CyberpunkRedAPI\AbstractRoute;
use Faker\Factory;
use Faker\Generator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class CompanyName -- Generates a random Company Name based on qualifiers
 * Example Routing: /company/{options}
 * @package CyberpunkRedAPI\Choose
 */
class CompanyName extends AbstractRoute
{
    protected Generator $faker;

    /**
     * PersonName constructor.
     * @param Factory $faker
     */
    public function __construct(Factory $faker)
    {
        $this->faker = $faker::create();
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        $name = $this->faker->company();

        $format = $request->getAttribute('format', 'json');

        return $this->outputResponse($response, ['companyName' => $name], $format);
    }
}
