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
        $movement[] = new MovementOrder(-1, "start", 1, 1, 0, 0, 1, 4, 4, true, 1, 0);
        $movement[] = new MovementOrder(-1, "move", 0, 0, 0, 0, 1, 4, 4, false, 1, 0);
        $ship = new Gquan(1, 1, "G'Quan", $movement);
        $ship->setMovements($movement);
        $ship->iniative = 1;
        $ships[] = $ship;
        
        $movement = Array();
        $movement[] = new MovementOrder(-1, "start", 1, 1, 0, 0, 1, 4, 4, true, 1, 0);
        $movement[] = new MovementOrder(-1, "move", 0, 0, 0, 0, 1, 4, 4, false, 1, 0);
        $ship = new Frazi(2, 1, 'Frazi', $movement);
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
        $shooterPos = $shooter->getCoPos();
        $weapon = new StdParticleBeam(2, 6, 1, 330, 60);
        
        Debug::log(var_export($shooterPos,true));
        Debug::log(var_export($target->getCoPos(),true));
        
        $this->assertTrue(BaseShip::hasBetterIniative($shooter, $target));
        $this->assertEquals(240, $target->getFacingAngle());
        $this->assertEquals(60, Mathlib::getCompassHeadingOfShip($target, $shooter));
        
        $this->assertEquals(2, $target->doGetHitSection($target->getFacingAngle(), Mathlib::getCompassHeadingOfShip($target, $shooter), 1, null));        
        $this->assertEquals(2, $target->getHitSection($shooter->getCoPos(), $shooter, 1, $weapon));
        $fire = new FireOrder(1, 'normal', $shooter->id, $target->id, 1, -1, 1, 1, 0, 0, 1, 0, 0, 0, 0);
        $system = $target->getHitSystem($shooter->getCoPos(), $shooter, $fire, $weapon, null);
        $this->assertEquals(2, $system->location);
    }
    
    public function tearDown() {
        parent::tearDown();
    }
}