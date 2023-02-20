<?php

namespace CyberpunkRedAPI\Choose;

use CyberpunkRedAPI\AbstractRoute;
use CyberpunkRedAPI\Generic\ListFactory;
use CyberpunkRedAPI\Generic\Record;
use CyberpunkRedAPI\Generic\RecordFactory;
use CyberpunkRedAPI\Generic\RecordList;
use Faker\Factory;
use Faker\Generator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class PersonName -- Generates a random Character Name based on qualifiers
 * Example Routing: /name/{gender}/{name portion: first, last, full}
 * @package ScionAPI\Choose
 */
class PersonName extends AbstractRoute
{
    protected Generator $faker;
    private RecordList $neutralNames;

    public function __construct(Factory $faker, ListFactory $listFactory, RecordFactory $recordFactory)
    {
        $this->faker = $faker::create();
        $fullList = $listFactory::create();
        $fullList->loadFile(__DIR__ . '/../../../json_src/neutralNames.json', false);
        $this->neutralNames = $fullList;
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function __invoke(Request $request, Response $response, array $args = []): Response
    {
        $gender = strtolower($request->getAttribute('gender'));

        $neutralName = false;
        $name = false;
        if ($gender === 'neutral') {
            /** @var Record $neutralNameRecord */
            $neutralNameRecord = $this->neutralNames->current();
            $selector = rand(0, $neutralNameRecord->count()-1);
            $neutralName = $neutralNameRecord->{'_' . $selector};
            $gender = '';
        }

        switch (strtolower($request->getAttribute('firstLastFull'))) {
            case 'first':
                $fname = $this->faker->firstName($gender);
                if ($neutralName) {
                    $fname = $neutralName;
                }
                $lname = null;
                break;
            case 'last':
                $fname = null;
                $lname = $this->faker->lastName;
                break;
            case 'full':
                $fname = $this->faker->firstName($gender);
                if ($neutralName) {
                    $fname = $neutralName;
                }
                $lname = $this->faker->lastName;
                break;
            default:
                if ($gender == 'first') {
                    $name = $this->faker->firstName;
                } elseif ($gender == 'last') {
                    $name = $this->faker->lastName;
                } elseif ($gender == 'full') {
                    $name = $this->faker->firstName . ' ' . $this->faker->lastName;
                } else {
                    $name = $this->faker->name($gender);
                }
        }

        if (!$name) {
            $name = trim($fname . ' ' . $lname);
        }

        $format = $request->getAttribute('format', 'json');

        return $this->outputResponse($response, ['name' => $name], $format);
    }
}
