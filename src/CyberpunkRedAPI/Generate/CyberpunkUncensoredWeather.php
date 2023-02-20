<?php


namespace CyberpunkRedAPI\Generate;


use CyberpunkRedAPI\AbstractRoute;
use CyberpunkRedAPI\Generic\ListFactory;
use CyberpunkRedAPI\Generic\RecordFactory;
use CyberpunkRedAPI\Generic\RecordList;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CyberpunkUncensoredWeather extends AbstractRoute
{
    private RecordList $allWeather;

    public function __construct(ListFactory $listFactory, RecordFactory $recordFactory)
    {
        $fullList = $listFactory::create();
        $fullList->loadFile(__DIR__ . '/../../../json_src/cpuWeather.json', false);
        $this->allWeather = $fullList;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(
        Request $request,
        Response $response,
        array $args = []
    ): Response {
        $currentWeather = [];
        $conditionRoll = rand(1,100);

        $conditions = $this->allWeather->getRecordByKey('_' . $conditionRoll);
        $currentWeather['Condition'] = $conditions->condition;
        $currentWeather['Description'] = $conditions->description;

        return $this->outputResponse($response, $currentWeather);
    }
}
