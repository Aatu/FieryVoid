<?php

class SystemData
{
    private static $allData = Array();
    private static $currentTurn = null;
    private static $currentGameId = null;

    public $systemid, $subsystem, $shipid, $gameid, $turn;
    public $data = Array();

    public function __construct($systemid, $subsystem, $shipid, $gameid, $turn)
    {
        $this->systemid = $systemid;
        $this->subsystem = $subsystem;
        $this->shipid = $shipid;
        $this->gameid = $gameid;
        $this->turn = $turn;
    }

    public function addData($data){
        $this->data[] = $data;
    }

    public function toJSON()
    {
        $json = "{".implode(",", $this->data)."}";
        return $json;
    }

    public static function getAndPurgeAllSystemData() {
        $data = self::$allData;
        self::$allData = [];
        return $data;
    }

    public static function initSystemData($currentTurn, $currentGameId) {
        self::$currentTurn = $currentTurn;
        self::$currentGameId = $currentGameId;
    }

    public static function addDataForSystem($systemid, $subsystem, $shipid, $data, $turn = null)
    {
        if (self::$currentTurn === null || self::$currentGameId === null) {
            throw new Exception("Systemdata is not initialized");
        }

        if ($turn === null) {
            $turn = self::$currentTurn;
        }

        // with new dualWeapon implementation: ignore subsystem
        if (!isset(self::$allData[$systemid."_0_".$shipid."_".$turn]))
        {
            $systemdata = new SystemData($systemid, $subsystem, $shipid, self::$currentGameId, $turn);
            $systemdata->addData($data);
            self::$allData[] = $systemdata;
        }
        else
        {
            self::$allData[$systemid."_0_".$shipid]->addData($data);
        }
    }
}