<?php

ini_set('display_errors',1);
error_reporting(E_ALL);
require_once dirname(__DIR__) . "/TestBase.php";
require_once dirname(__DIR__) . '/../source/autoload.php';
session_start();

class MovementOrderTest extends TestBase
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
        $ship = new Rutarian(1, 1, 'rutarian', $movement);
        $ship->setMovements($movement);
        $ship->iniative = 173;
        $ships[] = $ship;
        
        $movement = Array();
        $movement[] = new MovementOrder(-1, "start", -1, 0, 0, 0, 1, 0, 0, true, 1, 0);
        $movement[] = new MovementOrder(-1, "move", 0, 0, 0, 0, 1, 0, 0, false, 1, 0);
        $ship = new Nial(2, 1, 'nial', $movement);
        $ship->setMovements($movement);
        $ship->iniative = 173;
        $ships[] = $ship;
        
        $movement = Array();
        $movement[] = new MovementOrder(-1, "start", 1, 0, 0, 0, 1, 3, 3, true, 1, 0);
        $movement[] = new MovementOrder(-1, "move", 0, 0, 0, 0, 1, 3, 3, false, 1, 0);
        $ship = new Frazi(3, 1, 'frazi 1', $movement);
        $ship->setMovements($movement);
        $ship->iniative = 173;
        $ships[] = $ship;
        
        $movement = Array();
        $movement[] = new MovementOrder(-1, "start", -1, 0, 0, 0, 1, 0, 0, true, 1, 0);
        $movement[] = new MovementOrder(-1, "move", 0, 0, 0, 0, 1, 0, 0, false, 1, 0);
        $ship = new Frazi(4, 1, 'frazi 2', $movement);
        $ship->setMovements($movement);
        $ship->iniative = 173;
        $ships[] = $ship;
        
        $gamedata->setShips($ships);
        return $gamedata;
    }
    
    /**
     *  @test 
     */
    public function iniativeOrder()
    {
        $gamedata = $this->getGamedata();
        $gamedata->doSortShips();
        
        foreach ($gamedata->ships as $i=>$s)
        {
            if ($i===0)
                continue;
            
            $ls= $gamedata->ships[$i-1];
            
            $this->assertTrue(BaseShip::hasBetterIniative(
                $s, $ls),
                    $ls->name . "(iniative: ".$ls->iniative.", bonus: ". $ls->iniativebonus.")".
                    " has better iniative than ".
                    $s->name . "(iniative: ".$s->iniative.", bonus: ". $s->iniativebonus." but ".$ls->name." is posed to move earlier)"
                );
        }
        
    }
    
    public function tearDown() {
        parent::tearDown();
    }
}