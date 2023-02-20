<?php


namespace CyberpunkRedAPI\Generate;


use CyberpunkRedAPI\Generic\ListFactory;
use CyberpunkRedAPI\Generic\RecordFactory;
use CyberpunkRedAPI\Generic\RecordList;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use function Webmozart\Assert\Tests\StaticAnalysis\false;

class NightCityWeatherCustom extends NightCityWeather
{

    protected RecordList $cpuWeather;
    protected RecordList $nightCityWeather;

    public function __construct(ListFactory $listFactory, RecordFactory $recordFactory)
    {
        $fullNCList = $listFactory::create();
        $fullNCList->loadFile(__DIR__ . '/../../../json_src/ncWeatherStandard.json', false);
        $this->nightCityWeather = $fullNCList;

        $fullCPUList = $listFactory::create();
        $fullCPUList->loadFile(__DIR__ . '/../../../json_src/cpuWeather.json', false);
        $this->cpuWeather = $fullCPUList;
    }

    public function __invoke(
        Request $request,
        Response $response,
        array $args = []
    ): Response {
        $currentMonth = $this->getMonthRange(strtolower($request->getAttribute('month')));
        $currentWeather = [];
        $temp = rand(0,5);
        $currentMonthTemps = $this->nightCityWeather->getRecordByKey($currentMonth . '_temp');
        $currentWeather['Temperature'] = $currentMonthTemps->{'_' . $temp};
        $conditionRoll = rand(1,100);
        $conditions = $this->cpuWeather->getRecordByKey('_' . $conditionRoll);
        $currentWeather['Condition'] = $conditions->condition;
        $currentWeather['Description'] = $conditions->description;

        return $this->outputResponse($response, $currentWeather);
    }

}
