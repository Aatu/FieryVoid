<?php

require_once('../TestBase.php');


class GameCreationTest extends TestBase
{
    public function setUp() {
        parent::setUp();

    }

    private function getGamedata()
    {
        $gamedata = new TacGamedata(1, 1, 1, -1, 1, 'test', 'ACTIVE', 2000, '', 1);
        $ships = Array();

        $movement = Array();
        $movement[] = new MovementOrder(-1, "start",  new OffsetCoordinate(1, 1), 0, 0, 1, 4, 4, true, 1, 0);
        $movement[] = new MovementOrder(-1, "move",  new OffsetCoordinate(0, 0), 0, 0, 1, 4, 4, false, 1, 0);
        $ship = new Gquan(1, 1, "G'Quan", $movement);
        $ship->setMovements($movement);
        $ship->iniative = 1;
        $ships[] = $ship;

        $movement = Array();
        $movement[] = new MovementOrder(-1, "start",  new OffsetCoordinate(1, 1), 0, 0, 1, 4, 4, true, 1, 0);
        $movement[] = new MovementOrder(-1, "move",  new OffsetCoordinate(0, 0), 0, 0, 1, 4, 4, false, 1, 0);
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
    public function testCreateGame() {
        $gameId = Manager::createGame(1, $this->createGameData);
        $this->assertNotNull($gameId);
        $gameData = $this->getDatabase()->getTacGame($gameId, 1);
        $this->assertNotNull($gameData);
        //$this->getDatabase()->endTransaction(false, true);
    }

    /**
     * @test
     * @depends testCreateGame
     */
    public function testStartGame()
    {
        $gameId = Manager::createGame(1, $this->createGameData);
        $gameData = $this->getDatabase()->getTacGamedata(1, $gameId);
        $this->assertNotNull($gameData);
        $this->assertNotError(Manager::submitTacGamedata($gameId, 1, $gameData->turn, $gameData->phase, $gameData->activeship, json_encode($this->buyShipDataForUser1()), $gameData->status, 1));
        Manager::takeSlot(2, $gameId, 2);
        $this->assertNotError(Manager::submitTacGamedata($gameId, 2, $gameData->turn, $gameData->phase, $gameData->activeship, json_encode($this->buyShipDataForUser2()), $gameData->status, 2));


        $gameDataForPlayer1 = $this->getDatabase()->getTacGamedata(1, $gameId);
        $this->assertNotNull($gameDataForPlayer1);
        $gameDataForPlayer2 = $this->getDatabase()->getTacGamedata(1, $gameId);
        $this->assertNotNull($gameDataForPlayer2);
        $gameDataForPlayer1->ships;

        $this->assertTrue( $gameDataForPlayer1->ships[0] instanceof Gquan);
        $this->assertTrue( $gameDataForPlayer1->ships[1] instanceof Gquan);
        $this->assertTrue( $gameDataForPlayer1->ships[2] instanceof Gquan);
        $this->assertTrue( $gameDataForPlayer1->ships[3] instanceof Frazi);
        $this->assertTrue( count($gameDataForPlayer1->ships[3]->systems) === 5);
    }

    /*
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
    */

    public function tearDown() {
        parent::tearDown();
    }
}