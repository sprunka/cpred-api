<?php


namespace CyberpunkRedAPI\Generate;


use CyberpunkRedAPI\AbstractRoute;
use CyberpunkRedAPI\Generic\ListFactory;
use CyberpunkRedAPI\Generic\RecordFactory;
use CyberpunkRedAPI\Generic\RecordList;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class NightMarket extends AbstractRoute
{
    protected RecordList $fullNightMarket;
    protected array $allGoods;
    protected array $goodsTypes = [
        'Food and Drugs',
        'Personal Electronics',
        'Weapons and Armor',
        'Cyberware',
        'Clothing and Fashionware',
        'Survival Gear'
    ];

    public function __construct(ListFactory $listFactory, RecordFactory $recordFactory)
    {
        $fullList = $listFactory::create();
        $fullList->loadFile(__DIR__ . '/../../../json_src/nightMarketGoodTable.json', false);
        $this->fullNightMarket = $fullList;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(
        Request $request,
        Response $response,
        array $args = []
    ): Response {

        $goods = $this->populateBaseGoods();
        return $this->outputResponse($response, $goods);
    }

    protected function getSection(int $count, string $category): array
    {
        $hold = [];
        $test = rand(0, 19);
        $hold[] = $test;
        $sample = $this->allGoods[$category]->{'_' .$test} ;
        $section[] = $sample;
        for ($roll = 1; $roll < $count; $roll++) {
            while (in_array($test, $hold)) {
                $test = rand(0, 19);
            }
            $hold[] = $test;
            $sample = $this->allGoods[$category]->{'_' .$test} ;
            $section[] = $sample;
        }
        return $section;
    }

    protected function populateBaseGoods(): array
    {
        $firstGoods = rand(0,5);
        $secondGoods = rand(0,5);
        while ($firstGoods === $secondGoods) {
            $secondGoods = rand(0,5);
        }

        $this->allGoods[$this->goodsTypes[$firstGoods]] = $this->fullNightMarket->getRecordByKey($this->goodsTypes[$firstGoods]);
        $this->allGoods[$this->goodsTypes[$secondGoods]] = $this->fullNightMarket->getRecordByKey($this->goodsTypes[$secondGoods]);
        $goods = [];
        $firstGoodsCount = rand(1,10);
        $goods[$this->goodsTypes[$firstGoods]] = $this->getSection($firstGoodsCount, $this->goodsTypes[$firstGoods]);
        $secondGoodsCount = rand(1,10);
        $goods[$this->goodsTypes[$secondGoods]] = $this->getSection($secondGoodsCount, $this->goodsTypes[$secondGoods]);
        return $goods;
    }

}
