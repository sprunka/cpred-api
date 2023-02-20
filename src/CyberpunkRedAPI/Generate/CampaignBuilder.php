<?php


namespace CyberpunkRedAPI\Generate;


use CyberpunkRedAPI\AbstractRoute;
use CyberpunkRedAPI\Generic\ListFactory;
use CyberpunkRedAPI\Generic\RecordFactory;
use CyberpunkRedAPI\Generic\RecordList;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class CampaignBuilder extends AbstractRoute
{
    private RecordList $hooks;

    public function __construct(ListFactory $listFactory, RecordFactory $recordFactory)
    {
        $fullList = $listFactory::create();
        $fullList->loadFile(__DIR__ . '/../../../json_src/plotHooks.json', false);
        $this->hooks = $fullList;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(
        Request $request,
        Response $response,
        array $args = []
    ): Response {
        $hook = [];
        $hookRoll = rand(0,9);
        $hookComplicationRoll = rand(0,9);

        $hook['Plot'] = $this->getSubTable('base');
        if (strpos($hook['Plot'], '{CHARACTER}') !== false) {
            $hookCharacter = $this->getSubTable('character');
            $hook['Plot'] = str_replace('{CHARACTER}', $hookCharacter, $hook['Plot']);
            $hook['Plot Character Attitude'] = $this->getSubTable('character_attitude');
        }
        if (strpos($hook['Plot'], '{LOCATION}') !== false) {
            $hookCharacter = $this->getSubTable('location');
            $hook['Plot'] = str_replace('{LOCATION}', $hookCharacter, $hook['Plot']);
            $hook['Location Complication'] = $this->getSubTable('location_complications');
            if (strpos($hook['Location Complication'], '{CHARACTER}') !== false) {
                $hookCharacter = $this->getSubTable('character');
                $hook['Location Complication'] = str_replace('{CHARACTER}', $hookCharacter, $hook['Location Complication']);
                $hook['Location Character Attitude'] = $this->getSubTable('character_attitude');
            }
        }
        if (strpos($hook['Plot'], '{THING}') !== false) {
            $hookCharacter = $this->getSubTable('thing');
            $hook['Plot'] = str_replace('{THING}', $hookCharacter, $hook['Plot']);
            $hook['Item/Goods Condition'] = $this->getSubTable('thing_condition');
        }
        $hook['Plot Complication'] = $this->getSubTable('plot_complications');
        if (strpos($hook['Plot Complication'], '{CHARACTER}') !== false) {
            $hookCharacter = $this->getSubTable('character');
            $hook['Plot Complication'] = str_replace('{CHARACTER}', $hookCharacter, $hook['Plot Complication']);
            $hook['Plot Complication Character Attitude'] = $this->getSubTable('character_attitude');
        }

        return $this->outputResponse($response, $hook);
    }

    protected function getSubTable(string $table)
    {
        $index = rand(0,9);
        return $this->hooks->getRecordByKey($table)->{$index};
    }

}
