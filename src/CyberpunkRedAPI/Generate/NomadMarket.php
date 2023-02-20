<?php


namespace CyberpunkRedAPI\Generate;


use CyberpunkRedAPI\AbstractRoute;
use CyberpunkRedAPI\Generic\ListFactory;
use CyberpunkRedAPI\Generic\RecordFactory;
use CyberpunkRedAPI\Generic\RecordList;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NomadMarket extends AbstractRoute
{
    protected RecordList $fullNomadMarket;
    protected RecordList $fullNightMarket;
    protected array $allGoods;
    protected array $goodsOut;
    protected array $categories;
    protected array $nomadGoods;
    protected array $nightMarketGoods;

    public function __construct(ListFactory $listFactory, RecordFactory $recordFactory)
    {
        $this->categories = [];
        $fullList = $listFactory::create();
        $fullList->loadFile(__DIR__ . '/../../../json_src/nomadMarket.json', false);
        $this->fullNomadMarket = $fullList;

        $fullNMList = $listFactory::create();
        $fullNMList->loadFile(__DIR__ . '/../../../json_src/nightMarketGoodTable.json', false);
        $this->fullNightMarket = $fullNMList;

        $this->nomadGoods = [
            'Transport',
            'Medicine',
            'Weapons',
            'Upgrades',
            'Vehicles',
            'NightMarket'
        ];
        $this->nightMarketGoods = [
            'Food and Drugs',
            'Personal Electronics',
            'Weapons and Armor',
            'Cyberware',
            'Clothing and Fashionware',
            'Survival Gear'
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
        $specialty = $request->getAttribute('specialty');
        if (!empty($specialty) && $specialty != 'null' && $specialty !== '{specialty}') {
            $this->allGoods[$specialty] = $this->fullNomadMarket->getRecordByKey($specialty);
            $count = rand(1, 10);
            $this->goodsOut[$specialty] = $this->getSection($count, $specialty);
            return $this->outputResponse($response, $this->goodsOut);
        }

        $numberOfCategories = rand(1, 6);
        $goods = [];
        for ($catCount = 0; $catCount <= $numberOfCategories; $catCount++) {
            $catName = $this->getCategoryName();
            $fullCatName = $this->buildCategoryData($catName);
            $countGoods = rand(1, 10);
            $goods[$fullCatName] = $this->getSection($countGoods, $catName);
        }
        return $this->outputResponse($response, $goods);
    }

    protected function getCategoryName() : string
    {
        $index = rand(0, 5);
        $catName = $this->nomadGoods[$index];
        while (!in_array($catName, $this->categories)) {
            $this->categories[] = $catName;
        }
        return $catName;

    }

    protected function buildCategoryData(string $catName) : string
    {
        $this->allGoods[$catName] = $this->fullNomadMarket->getRecordByKey($catName);
        $fullCatName = $catName;
        if ($catName === 'NightMarket') {
            $altGoods = rand(0,5);
            $fullCatName = 'Night Market - ' . $this->nightMarketGoods[$altGoods];
            $this->allGoods[$catName] = $this->fullNightMarket->getRecordByKey($this->nightMarketGoods[$altGoods]);
        }
        return $fullCatName;
    }

    private function getSection(int $count, string $category): array
    {
        $hold = [];
        $test = rand(0, 9);
        $hold[] = $test;
        $sample = $this->allGoods[$category]->{'_' .$test} ;
        $section[] = $sample;
        for ($roll = 1; $roll < $count; $roll++) {
            while (in_array($test, $hold)) {
                $test = rand(0, 9);
            }
            $hold[] = $test;
            $sample = $this->allGoods[$category]->{'_' .$test} ;
            $section[] = $sample;
        }
        return $section;
    }
}
