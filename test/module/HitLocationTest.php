<?php

ini_set('display_errors',1);
error_reporting(E_ALL);
require_once dirname(__DIR__) . "/TestBase.php";
require_once dirname(__DIR__) . '/../source/autoload.php';
session_start();

class HitLocationTest extends TestBase
{
    public function setUp() {
        parent::setUp();
        
    }
    
    private function getGamedata()
    {
        $gamedata = new TacGamedata(1, 1, 1, -1, 1, 'test', 'ACTIVE', 2000, '', 1);
        $ships = Array();
        
        $movement = Array();
        $movement[] = new MovementOrder(-1, "start", 1, 0, 0, 0, 1, 3, 3, true, 1, 0);
        $movement[] = new MovementOrder(-1, "move", 0, 0, 0, 0, 1, 3, 3, false, 1, 0);
        $ship = new Gquan(1, 1, "G'Quan", $movement);
        $ship->setMovements($movement);
        $ship->iniative = 1;
        $ships[] = $ship;
        
        $movement = Array();
        $movement[] = new MovementOrder(-1, "start", -1, 0, 0, 0, 1, 0, 0, true, 1, 0);
        $movement[] = new MovementOrder(-1, "move", 0, 0, 0, 0, 1, 0, 0, false, 1, 0);
        $ship = new Primus(2, 1, 'Primus', $movement);
        $ship->setMovements($movement);
        $ship->iniative = 2;
        $ships[] = $ship;
        
        
        $gamedata->setShips($ships);
        return $gamedata;
    }
    
    /**
     *  @test 
     */
    public function hitLocation()
    {
        $gamedata = $this->getGamedata();
        $gamedata->doSortShips();
        
        $target = $gamedata->getShipById(1);
        $shooter = $gamedata->getShipById(2);
        
    }
    
    public function tearDown() {
        parent::tearDown();
    }
}