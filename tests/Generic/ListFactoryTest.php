<?php

namespace Generic;

use CyberpunkRedAPI\Generic\ListFactory;
use PHPUnit\Framework\TestCase;

class ListFactoryTest extends TestCase
{

    public function testCreateEmpty()
    {
        $testList = ListFactory::create();
        $this->assertIsObject($testList);
        $this->assertIsIterable($testList);
        $this->assertEquals('CyberpunkRedAPI\Generic\RecordList', $testList::class);

    }

    public function testCreateFromJSONString()
    {
        $data = '{"foo":"bar"}';

        $testList = ListFactory::create($data);
        $this->assertIsObject($testList);
        $this->assertIsIterable($testList);
        $this->assertEquals('CyberpunkRedAPI\Generic\RecordList', $testList::class);

    }

    public function testCreateFromArray()
    {
        $data = ["foo"=>"bar"];

        $testList = ListFactory::create($data);
        $this->assertIsObject($testList);
        $this->assertIsIterable($testList);
        $this->assertEquals('CyberpunkRedAPI\Generic\RecordList', $testList::class);

    }

    public function testCreateFromFile()
    {
        $data = __DIR__ . '/../../json_src/testJSON.json';

        $testList = ListFactory::create($data);
        $this->assertIsObject($testList);
        $this->assertIsIterable($testList);
        $this->assertEquals('CyberpunkRedAPI\Generic\RecordList', $testList::class);

    }
}
