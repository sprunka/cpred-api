<?php


namespace CyberpunkRedAPI\Generate;


use CyberpunkRedAPI\Generic\ListFactory;
use CyberpunkRedAPI\Generic\RecordFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NightMarketVoR extends NightMarket
{
    public function __construct(ListFactory $listFactory, RecordFactory $recordFactory)
    {
        $fullList = $listFactory::create();
        $fullList->loadFile(__DIR__ . '/../../../json_src/nightMarketVOR.json', false);
        $this->fullNightMarket = $fullList;
        $this->goodsTypes = [
            'Food and Drugs',
            'Personal Electronics',
            'Weapons and Armor',
            'Cyberware',
            'Clothing and Fashionware',
            'Survival Gear',
            'VoR Vehicles',
            'VoR Vehicle Upgrades',
            'VoR Vehicle Weapons'
        ];
    }

    /**
     * @inheritDoc
     */
    public function __invoke(
        Request $request,
        Response $response,
        array $args = []
    ): Response {
        $nomadRank = $request->getAttribute('nomad');
        $goods = $this->populateBaseGoods();

        if ($nomadRank >= 1) {
            $cat1 = rand(6, 8);
            $firstNomadCategory = $this->goodsTypes[$cat1];
            $this->allGoods[$firstNomadCategory] = $this->fullNightMarket->getRecordByKey($firstNomadCategory);
            $count1 = rand(1,10);
            $goods[$firstNomadCategory] = $this->getSection($count1, $firstNomadCategory);
        }
        if ($nomadRank >= 5) {
            $cat2 = $cat1;
            while ($cat2 === $cat1) {
                $cat2 = rand(6, 8);
                $secondNomadCategory = $this->goodsTypes[$cat2];
                $this->allGoods[$secondNomadCategory] = $this->fullNightMarket->getRecordByKey($secondNomadCategory);
                $count2 = rand(1, 10);
                $goods[$secondNomadCategory] = $this->getSection($count2, $secondNomadCategory);
            }
        }
        if ($nomadRank >= 8) {
            $cat3 = (6+7+8) - ($cat1 + $cat2);
            $thirdNomadCategory = $this->goodsTypes[$cat3];
            $this->allGoods[$thirdNomadCategory] = $this->fullNightMarket->getRecordByKey($thirdNomadCategory);
            $count3 = rand(1, 10);
            $goods[$thirdNomadCategory] = $this->getSection($count3, $thirdNomadCategory);
        }
        return $this->outputResponse($response, $goods);
    }
}
