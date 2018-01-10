<?php

require_once('../TestBase.php');

class EWTest extends TestBase
{
    /**
     * @test
     */
    public function testValidEW() {
        $gameId = $this->createTestGameForInitialOrders();
        $gameData1 = $this->getDatabase()->getTacGamedata(1, $gameId);
        $gameData2 = $this->getDatabase()->getTacGamedata(1, $gameId);
        $this->assertNotError(Manager::submitTacGamedata($gameId, 1, $gameData1->turn, $gameData1->phase, $gameData1->activeship, json_encode($this->ewShipDataForUser1($gameData1)), $gameData1->status, 1));
        $this->assertNotError(Manager::submitTacGamedata($gameId, 2, $gameData2->turn, $gameData2->phase, $gameData2->activeship, json_encode($this->ewShipDataForUser2($gameData2)), $gameData2->status, 2));
        Manager::advanceGameState(1, $gameId);
        $newGameData = $this->getDatabase()->getTacGamedata(1, $gameId);
        $this->assertTrue($newGameData->phase === 2);
    }
}