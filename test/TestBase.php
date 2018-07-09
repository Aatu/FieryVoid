<?php

ini_set('display_errors',1);
error_reporting(E_ALL);
require_once dirname(__DIR__) . '/source/autoload.php';
session_start();
require_once dirname(__DIR__) . '/source/server/varconfig.php' ;


use PHPUnit\Framework\TestCase;

class TestBase extends TestCase
{
    private static $dbManager = null;

    protected function getDatabase() {
        global $database_host;
        global $database_name;
        global $database_user;
        global $database_password;
        if (self::$dbManager == null)
            self::$dbManager = new DBManager($database_host ?? "localhost", 3306, $database_name, $database_user, $database_password, true);

        return self::$dbManager;
    }

    public function setUp() {
        parent::setUp();
        Manager::setDBManager($this->getDatabase());
        $this->getDatabase()->startTransaction();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->getDatabase()->endTransaction(true);
    }

    protected $createGameData = '{"gamename":"Test game","background":"3d_space_80.jpg","slots":[{"id":1,"team":1,"name":"BLUE","points":3500,"depx":-21,"depy":0,"deptype":"box","depwidth":10,"depheight":30,"depavailable":0},{"id":2,"team":2,"name":"RED","points":3500,"depx":21,"depy":0,"deptype":"box","depwidth":10,"depheight":30,"depavailable":0}],"gamespace":"-1x-1","flight":""}';

    protected function buyShipDataForUser1()
    {
        return [
            ["phpclass"=> "Gquan", "id"=> 1, "userid"=> 1, "name"=> "test ship 3", "slot"=> 1],
            ["phpclass"=> "frazi", "id"=> 2, "userid"=> 1, "name"=> "test flight", "slot"=> 1, "flightSize"=> 5]
        ];
    }

    protected function buyShipDataForUser2() {
        return [
            ["phpclass"=> "Gquan", "id"=> 3, "userid"=> 2, "name"=> "test ship 1", "slot"=> 2],
            ["phpclass"=> "Gquan", "id"=> 4, "userid"=> 2, "name"=> "test ship 2", "slot"=> 2]
        ];
    }

    protected function deploymentShipDataForUser1(TacGamedata $gameData) {
        $ships = $this->getShipsForUser($gameData, 1);

        $ships[0]->movement = [
            ["id"=>1336740,"type"=>"start","position"=>["q"=>-30,"r"=>0],"xOffset"=>0,"yOffset"=>0,"facing"=>0,"heading"=>0,"speed"=>5,"value"=>0,"at_initiative"=>0,"animating"=>false,"animated"=>true,"animationtics"=>0,"preturn"=>1,"requiredThrust"=>[0,0,0,0,0],"assignedThrust"=>[],"commit"=>true,"turn"=>1,"forced"=>false],
            ["id"=>-1,"type"=>"deploy","position"=>["q"=>-19,"r"=>0,"layout"=>1],"xOffset"=>0,"yOffset"=>0,"facing"=>1,"heading"=>1,"speed"=>6,"animating"=>false,"animated"=>true,"animationtics"=>0,"requiredThrust"=>[null,null,null,null,null],"assignedThrust"=>[],"commit"=>true,"preturn"=>false,"at_initiative"=>2,"turn"=>1,"forced"=>false,"value"=>0]
        ];

        $ships[1]->movement = [
            ["id"=>1336741,"type"=>"start","position"=>["q"=>-30,"r"=>1],"xOffset"=>0,"yOffset"=>0,"facing"=>0,"heading"=>0,"speed"=>5,"value"=>0,"at_initiative"=>0,"animating"=>false,"animated"=>true,"animationtics"=>0,"preturn"=>1,"requiredThrust"=>[0,0,0,0,0],"assignedThrust"=>[],"commit"=>true,"turn"=>1,"forced"=>false],
            ["id"=>-1,"type"=>"deploy","position"=>["q"=>-19,"r"=>2,"layout"=>1],"xOffset"=>0,"yOffset"=>0,"facing"=>0,"heading"=>0,"speed"=>7,"animating"=>false,"animated"=>true,"animationtics"=>0,"requiredThrust"=>[null,null,null,null,null],"assignedThrust"=>[],"commit"=>true,"preturn"=>false,"at_initiative"=>4,"turn"=>1,"forced"=>false,"value"=>0]
        ];
        return $ships;
    }

    protected function deploymentShipDataForUser2(TacGamedata $gameData){
        $ships = $this->getShipsForUser($gameData, 2);

        $ships[0]->movement = [
            ["id"=>1336739,"type"=>"start","position"=>["q"=>30,"r"=>0],"xOffset"=>0,"yOffset"=>0,"facing"=>3,"heading"=>3,"speed"=>5,"value"=>0,"at_initiative"=>0,"animating"=>false,"animated"=>true,"animationtics"=>0,"preturn"=>1,"requiredThrust"=>[0,0,0,0,0],"assignedThrust"=>[],"commit"=>true,"turn"=>1,"forced"=>false],
            ["id"=>-1,"type"=>"deploy","position"=>["q"=>17,"r"=>-14,"layout"=>1],"xOffset"=>0,"yOffset"=>0,"facing"=>3,"heading"=>3,"speed"=>5,"animating"=>false,"animated"=>true,"animationtics"=>0,"requiredThrust"=>[null,null,null,null,null],"assignedThrust"=>[],"commit"=>true,"preturn"=>false,"at_initiative"=>1,"turn"=>1,"forced"=>false,"value"=>0]
        ];

        $ships[1]->movement = [
            ["id"=>1336742,"type"=>"start","position"=>["q"=>30,"r"=>1],"xOffset"=>0,"yOffset"=>0,"facing"=>3,"heading"=>3,"speed"=>5,"value"=>0,"at_initiative"=>0,"animating"=>false,"animated"=>true,"animationtics"=>0,"preturn"=>1,"requiredThrust"=>[0,0,0,0,0],"assignedThrust"=>[],"commit"=>true,"turn"=>1,"forced"=>false],
            ["id"=>-1,"type"=>"deploy","position"=>["q"=>25,"r"=>14,"layout"=>1],"xOffset"=>0,"yOffset"=>0,"facing"=>3,"heading"=>3,"speed"=>5,"animating"=>false,"animated"=>true,"animationtics"=>0,"requiredThrust"=>[null,null,null,null,null],"assignedThrust"=>[],"commit"=>true,"preturn"=>false,"at_initiative"=>3,"turn"=>1,"forced"=>false,"value"=>0]
        ];

        return $ships;
    }

    protected function ewShipDataForUser1(TacGamedata $gameData) {
        $ships = $this->getShipsForUser($gameData, 1);
        $enemyShips = $this->getShipsForUser($gameData, 2);

        $ships[0]->ew = [
            ["shipid"=> $ships[0]->id, "type"=> "OEW", "amount"=> 5, "targetid"=> $enemyShips[0]->id, "turn"=> 1],
            ["shipid"=> $ships[0]->id, "type"=> "CCEW", "amount"=> 1, "targetid"=> -1, "turn"=> 1]
        ];

        $ships[1]->ew = [];
        return $ships;
    }

    protected function ewShipDataForUser2(TacGamedata $gameData) {
        $ships = $this->getShipsForUser($gameData, 2);
        $ships[0]->ew = [];
        $ships[1]->ew = [];
        return $ships;
    }

    protected function getShipsForUser(TacGamedata $gamedata, int $userId) {
        $returnShips = [];
        foreach ($gamedata->ships as $ship) {
           if ($ship->userid === $userId) {
               $returnShips[] = $ship;
           }
        }

        return $returnShips;
    }

    protected function createTestGameForDeployment() {
        $gameId = Manager::createGame(1, $this->createGameData);
        $gameData = $this->getDatabase()->getTacGamedata(1, $gameId);
        $this->assertNotNull($gameData);
        $this->assertNotError(Manager::submitTacGamedata($gameId, 1, $gameData->turn, $gameData->phase, $gameData->activeship, json_encode($this->buyShipDataForUser1()), $gameData->status, 1));
        Manager::takeSlot(2, $gameId, 2);
        $this->assertNotError(Manager::submitTacGamedata($gameId, 2, $gameData->turn, $gameData->phase, $gameData->activeship, json_encode($this->buyShipDataForUser2()), $gameData->status, 2));
        Manager::advanceGameState(1, $gameId);
        return $gameId;
    }

    protected function createTestGameForInitialOrders()  {
        $gameId = $this->createTestGameForDeployment();
        $gameData1 = $this->getDatabase()->getTacGamedata(1, $gameId);
        $gameData2 = $this->getDatabase()->getTacGamedata(1, $gameId);
        $this->assertNotError(Manager::submitTacGamedata($gameId, 1, $gameData1->turn, $gameData1->phase, $gameData1->activeship, json_encode($this->deploymentShipDataForUser1($gameData1)), $gameData1->status, 1));
        $this->assertNotError(Manager::submitTacGamedata($gameId, 2, $gameData2->turn, $gameData2->phase, $gameData2->activeship, json_encode($this->deploymentShipDataForUser2($gameData2)), $gameData2->status, 2));
        Manager::advanceGameState(1, $gameId);
        return $gameId;
    }
    protected function assertNotError($json) {
        $data = json_decode($json, true);
        if (isset($data["error"])) {
            throw new Exception($data["error"]);
        }
        $this->assertFalse(isset($data["error"]));

    }
}