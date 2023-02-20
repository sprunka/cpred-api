<?php


namespace CyberpunkRedAPI\Generate;


use CyberpunkRedAPI\AbstractRoute;
use CyberpunkRedAPI\Generic\ListFactory;
use CyberpunkRedAPI\Generic\RecordFactory;
use CyberpunkRedAPI\Generic\RecordList;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NightCityWeather extends AbstractRoute
{
    private RecordList $allWeather;
    private array $weatherOut;

    public function __construct(ListFactory $listFactory, RecordFactory $recordFactory)
    {
        $fullList = $listFactory::create();
        $fullList->loadFile(__DIR__ . '/../../../json_src/ncWeatherStandard.json', false);
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
        $currentMonth = $this->getMonthRange(strtolower($request->getAttribute('month')));
        $currentWeather = [];

        $temp = rand(0,5);
        $conditions = rand(0,5);

        $this->weatherOut[$currentMonth . '_temp'] = $this->allWeather->getRecordByKey($currentMonth . '_temp');
        $currentWeather['Temperature'] = $this->weatherOut[$currentMonth . '_temp']->{'_' . $temp};

        $strangeDuration = false;
        $baseCondition = false;
        $strangeCondition = '';
        if ($conditions === 5) {
            $baseCondition = $this->allWeather->getRecordByKey($currentMonth . '_cond')->{'_' . rand(0, 4)};
            $currentMonth = 'Strange';
            $conditions = rand(0, 9);
            $strangeDuration = rand(0, 9);
            $strangeCondition = 'Strange Condition: ';
        }

        $this->weatherOut[$currentMonth . '_cond'] = $this->allWeather->getRecordByKey($currentMonth . '_cond');
        $currentWeather['Conditions'] = $strangeCondition . $this->weatherOut[$currentMonth . '_cond']->{'_' . $conditions};

        if ($strangeDuration !== false && $baseCondition !== false) {
            $this->weatherOut[$currentMonth . '_time'] = $this->allWeather->getRecordByKey($currentMonth . '_time');
            $currentWeather['Condition Duration'] = $this->weatherOut[$currentMonth . '_time']->{'_' . $strangeDuration};
            $currentWeather['Underlying Conditions'] = $baseCondition;
        }

        return $this->outputResponse($response, $currentWeather);
    }

    protected function getMonthRange($currentMonth) : string
    {
        if ($currentMonth == 12 || $currentMonth == 1 || $currentMonth == 2) {
            $currentMonth = 'Dec-Feb';
        }
        if ($currentMonth == 3 || $currentMonth == 4 || $currentMonth == 5) {
            $currentMonth = 'Mar-May';
        }
        if ($currentMonth == 6 || $currentMonth == 7 || $currentMonth == 8) {
            $currentMonth = 'Jun-Aug';
        }
        if ($currentMonth == 9 || $currentMonth == 10 || $currentMonth == 11) {
            $currentMonth = 'Sep-Nov';
        }
        return $currentMonth;

    }
}
