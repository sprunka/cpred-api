<?php


namespace CyberpunkRedAPI\Generic;


class ListFactory
{
    public static function create($data = false, $autoSave = false, $createFile = false): RecordList
    {
        return new RecordList($data, $autoSave, $createFile);
    }

}
