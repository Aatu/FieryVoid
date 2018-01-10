<?php

require_once('../TestBase.php');

class DeploymentTest extends TestBase
{
    /**
     * @test
     */
    public function testDeployment() {
        $gameId = $this->createTestGameForDeployment();
        $gameData1 = $this->getDatabase()->getTacGamedata(1, $gameId);
        $gameData2 = $this->getDatabase()->getTacGamedata(1, $gameId);
        $this->assertNotError(Manager::submitTacGamedata($gameId, 1, $gameData1->turn, $gameData1->phase, $gameData1->activeship, json_encode($this->deploymentShipDataForUser1($gameData1)), $gameData1->status, 1));
        $this->assertNotError(Manager::submitTacGamedata($gameId, 2, $gameData2->turn, $gameData2->phase, $gameData2->activeship, json_encode($this->deploymentShipDataForUser2($gameData2)), $gameData2->status, 2));
        Manager::advanceGameState(1, $gameId);
        $newGameData = $this->getDatabase()->getTacGamedata(1, $gameId);
        $this->assertTrue($newGameData->phase === 1);
    }

    public function invalidDeploymentProvider() {
        return [
            [["q"=>-300,"r"=>2]],
            [["q"=>-15,"r"=>-15]],
            [["q"=>-16,"r"=>-15]],
            [["q"=>-16,"r"=>-14]],
            [["q"=>-26,"r"=>-14]],
            [["q"=>-26,"r"=>-15]],
            [["q"=>-25,"r"=>-15]],
            [["q"=>-26,"r"=>14]],
            [["q"=>-26,"r"=>15]],
            [["q"=>-25,"r"=>15]],
            [["q"=>-15,"r"=>15]],
            [["q"=>-16,"r"=>15]],
            [["q"=>-16,"r"=>14]],
        ];
    }

    /**
     * @test
     * @dataProvider invalidDeploymentProvider
     */
    public function testDeployedOutOfArea($position) {
        $this->expectExceptionMessage("Deployment validation failed: Illegal placement. Ship: test ship 3(".$position["q"].",".$position["r"].")");
        $gameId = $this->createTestGameForDeployment();
        $gameData1 = $this->getDatabase()->getTacGamedata(1, $gameId);
        $this->assertNotError(Manager::submitTacGamedata($gameId, 1, $gameData1->turn, $gameData1->phase, $gameData1->activeship, json_encode($this->invalidDeployment($gameData1, $position)), $gameData1->status, 1));


    }

    private function invalidDeployment(TacGamedata $gameData, $position) {
        $ships = $this->getShipsForUser($gameData, 1);

        $ships[0]->movement = [
            ["id"=>1336740,"type"=>"start","position"=>["q"=>-30,"r"=>0],"xOffset"=>0,"yOffset"=>0,"facing"=>0,"heading"=>0,"speed"=>5,"value"=>0,"at_initiative"=>0,"animating"=>false,"animated"=>true,"animationtics"=>0,"preturn"=>1,"requiredThrust"=>[0,0,0,0,0],"assignedThrust"=>[],"commit"=>true,"turn"=>1,"forced"=>false],
            ["id"=>-1,"type"=>"deploy","position"=>$position,"xOffset"=>0,"yOffset"=>0,"facing"=>1,"heading"=>1,"speed"=>6,"animating"=>false,"animated"=>true,"animationtics"=>0,"requiredThrust"=>[null,null,null,null,null],"assignedThrust"=>[],"commit"=>true,"preturn"=>false,"at_initiative"=>2,"turn"=>1,"forced"=>false,"value"=>0]
        ];

        $ships[1]->movement = [
            ["id"=>1336741,"type"=>"start","position"=>["q"=>-30,"r"=>1],"xOffset"=>0,"yOffset"=>0,"facing"=>0,"heading"=>0,"speed"=>5,"value"=>0,"at_initiative"=>0,"animating"=>false,"animated"=>true,"animationtics"=>0,"preturn"=>1,"requiredThrust"=>[0,0,0,0,0],"assignedThrust"=>[],"commit"=>true,"turn"=>1,"forced"=>false],
            ["id"=>-1,"type"=>"deploy","position"=>["q"=>-19,"r"=>2,"layout"=>1],"xOffset"=>0,"yOffset"=>0,"facing"=>0,"heading"=>0,"speed"=>7,"animating"=>false,"animated"=>true,"animationtics"=>0,"requiredThrust"=>[null,null,null,null,null],"assignedThrust"=>[],"commit"=>true,"preturn"=>false,"at_initiative"=>4,"turn"=>1,"forced"=>false,"value"=>0]
        ];
        return $ships;
    }
}