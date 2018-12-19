<?php
mysqli_report(MYSQLI_REPORT_ERROR);

class DBManager
{

    private $connection = null;
    private $testMode = false;

    function __construct($host, $port = 3306, $database, $username, $password, $testMode = false)
    {
        $this->id = uniqid();
        $this->testMode = $testMode;
        if ($this->connection !== null)
            return $this->connection;

        if (!$this->connection = mysqli_connect($host, $username, $password, $database, $port))
            throw new CustomException(300, "DBManager:Construct, connection failed: " . mysqli_connect_error(), mysqli_connect_errno(), null);

        if (!mysqli_select_db($this->connection, $database))
            throw new CustomException(300, "DBManager:Construct, connection failed: " . mysqli_error($this->connection), mysqli_errno(), null);

        mysqli_set_charset($this->connection, 'utf8');
    }

    private function DBEscape($string)
    {

        return mysqli_real_escape_string($this->connection, (String)$string);
    }


    public function __destruct()
    {
        $this->close();
    }

    private function query($sql)
    {


        if (!$this->connection)
            throw new Exception("DBManager:query, connection failed");

        if (!$answer = mysqli_query($this->connection, $sql)) {
            throw new Exception("DBManager:query, SQL error: " . mysql_error($this->connection) . "\n sql: $sql error:", mysql_errno($this->connection));
        }

        $result = array();

        while ($row = mysqli_fetch_object($answer)) {
            $result[] = $row;
        }

        return $result;
    }

    private function insert($sql)
    {


        if (!$this->connection)
            throw new exception("DBManager:insert, connection failed");

        if (!$answer = mysqli_query($this->connection, $sql))
            throw new exception("DBManager:insert, SQL error: " . mysqli_error($this->connection) . "\n sql: $sql" . mysqli_errno($this->connection));

        return $this->getLastInstertID();


    }

    private function getLastInstertID()
    {
        $sql = "select LAST_INSERT_ID() as id";

        if (!$answer = mysqli_query($this->connection, $sql))
            throw new exception("DBManager:insert, SQL (getting the id) error: " . mysqli_error($this->connection) . "\n sql: $sql", mysqli_errno($this->connection));


        while ($row = mysqli_fetch_object($answer)) {
            return $row->id;
        }

        return null;
    }

    public function update($sql)
    {


        if (!$this->connection)
            throw new exception("DBManager:update, connection failed");

        if (!$answer = mysqli_query($this->connection, $sql)) {
            throw new exception("DBManager:update, SQL error: " . mysqli_error($this->connection) . "\n sql: $sql", mysqli_errno($this->connection));
        }


    }

    private function found($sql)
    {
        $result = $this->query($sql);

        if ($result != null && sizeof($result) > 0)
            return true;

        return false;
    }

    public function startTransaction()
    {
        //mysqlii_query("SET AUTOCOMMIT=0", $this->connection);
        //mysqlii_query("START TRANSACTION", $this->connection);
        mysqli_autocommit($this->connection, FALSE);
    }

    public function endTransaction($rollback = false, $force = false)
    {
        if ($rollback == true) {
            mysqli_rollback($this->connection);
            mysqli_autocommit($this->connection, TRUE);
        } else if (!$this->testMode || $force) {
            mysqli_commit($this->connection);
            mysqli_autocommit($this->connection, TRUE);
        }
    }

    public function close()
    {
        mysqli_close($this->connection);
    }

    public function getActiveGames() {
        $games = [];
        $sql = "select distinct(gameid), g.name from tac_playeringame p join tac_game g on p.gameid = g.id where lastActivity > now() - interval 1 week";

        $stmt = $this->connection->prepare($sql);

        if ($stmt) {
            $stmt->bind_result($id, $name);
            $stmt->execute();
            while ($stmt->fetch()) {
                $games[] = ["id" => $id, "name" => $name];
            }
            $stmt->close();
        }

        return $games;
    }

    public function submitShip($gameid, $ship, $userid)
    {
        $sql = "INSERT INTO `B5CGM`.`tac_ship` VALUES(null, $userid, $gameid, '" . $this->DBEscape($ship->name) . "', '" . $ship->phpclass . "', 0, 0, 0, 0, 0, $ship->slot)";
        //   Debug::log($sql);
        $id = $this->insert($sql);
        return $id;
    }


    public function submitAdaptiveArmour($gameid, $shipid)
    {

        $sql = "INSERT INTO `B5CGM`.`tac_adaptivearmour` (gameid, shipid, particlepoints, particlealloc, laserpoints, laseralloc, molecularpoints, molecularalloc, matterpoints, matteralloc, plasmapoints, plasmaalloc, electromagneticpoints, electromagneticalloc, antimatterpoints, antimatteralloc, ionpoints, ionalloc, graviticpoints, graviticalloc, ballisticpoints, ballisticalloc)
            VALUES ($gameid, $shipid, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)";

        $id = $this->insert($sql);

    }

    public function submitFlightSize($gameid, $shipid, $flightSize)
    {
        $sql = "INSERT INTO `B5CGM`.`tac_flightsize` (gameid, shipid, flightsize)
            VALUES ($gameid, $shipid, $flightSize)";

        $id = $this->insert($sql);
    }


    public function submitAmmo($shipid, $systemid, $gameid, $firingMode, $ammoAmount, $turn)
    {
        $stmt = $this->connection->prepare("
            INSERT INTO 
              tac_ammo
            VALUES
                (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                firingmode = ?, ammo = ?
            
        ");

        $stmt->bind_param(
            'iiiiiiii',
            $shipid, $systemid, $firingMode, $gameid, $ammoAmount, $turn, $firingMode, $ammoAmount
        );
        $stmt->execute();
        $stmt->close();
    }

    public function deleteEmptyGames()
    {
        $ids = array();
        $stmt = $this->connection->prepare("SELECT gameid, count(playerid) as players FROM tac_playeringame GROUP BY gameid HAVING players = 0");

        if ($stmt) {
            $stmt->bind_result($id, $playerid);
            $stmt->execute();
            while ($stmt->fetch()) {
                $ids[] = $id;
            }
            $stmt->close();
        }

        $this->deleteGames($ids);
    }

    public function leaveSlot($userid, $gameid, $slotid = null)
    {

        $userid = $this->DBEscape($userid);
        $gameid = $this->DBEscape($gameid);
        $slotid = $this->DBEscape($slotid);

        try {

            $sql = "DELETE FROM `B5CGM`.`tac_ship` WHERE tacgameid = $gameid AND playerid = $userid";
            if ($slotid)
                $sql .= " AND slot = $slotid";

            $this->update($sql);

            $sql = "UPDATE tac_playeringame SET playerid = null, lastphase = -3, lastturn = 0 WHERE gameid = $gameid AND playerid = $userid";
            if ($slotid)
                $sql .= " AND slot = $slotid";

            $this->update($sql);


        } catch (Exception $e) {
            throw $e;
        }
    }


    public function shouldBeInGameLobby($userid)
    {

        try {
            $sql = "SELECT * FROM `B5CGM`.`tac_game` g join `B5CGM`.`tac_playeringame` p on g.id = p.gameid where p.playerid = $userid and g.status = 'LOBBY';";

            $result = $this->query($sql);

            if ($result == null || sizeof($result) == 0)
                return false;

            return $result[0]->id;
        } catch (Exception $e) {
            throw $e;
        }

    }

    public function takeSlot($userid, $gameid, $slotid)
    {

        $userid = $this->DBEscape($userid);
        $gameid = $this->DBEscape($gameid);
        $slotid = $this->DBEscape($slotid);
        try {

            $slot = $this->getSlotById($slotid, $gameid);
            if (!$slot)
                return false;

            //already in slot on other team?
            $sql = "SELECT * FROM `B5CGM`.`tac_playeringame` WHERE gameid = $gameid AND teamid != " . $slot->team . " AND playerid = $userid";
            if ($this->found($sql)) {
                $this->leaveSlot($userid, $gameid);
            }

            $sql = "UPDATE tac_playeringame SET playerid = $userid WHERE gameid = $gameid and slot = $slotid";

            $this->update($sql);


        } catch (Exception $e) {
            throw $e;
        }
    }

    public function createSlots($gameid, $input)
    {
        $slots = array();
        if (is_array($input))
            $slots = $input;
        else
            $slots[] = $input;

        $stmt = $this->connection->prepare("
            INSERT INTO 
                tac_playeringame
            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                now(),
                null,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                false
            )

        ");

        if ($stmt) {
            foreach ($slots as $slot) {
                $stmt->bind_param(
                    'iiiiiisiiisiii',
                    $gameid,
                    $slot->slot,
                    $slot->playerid,
                    $slot->team,
                    $slot->lastturn,
                    $slot->lastphase,
                    $slot->name,
                    $slot->points,
                    $slot->depx,
                    $slot->depy,
                    $slot->deptype,
                    $slot->depwidth,
                    $slot->depheight,
                    $slot->depavailable
                );
                $stmt->execute();
            }
            $stmt->close();
        }
    }

    public function createGame($gamename, $background, $slots, $userid, $gamespace, $rules = '{}')
    {
        $stmt = $this->connection->prepare("
            INSERT INTO 
                tac_game
            VALUES
            (
                null,
                ?,
                0,
                -2,
                '-1',
                ?,
                0,
                'LOBBY',
                ?,
                ?,
                null,
                ?,
                ?
            )
        ");

        if ($stmt) {
            $gamename = $this->DBEscape($gamename);
            $background = $this->DBEscape($background);
            $slotnum = count($slots);
            $gamespace = $this->DBEscape($gamespace);
            $stmt->bind_param(
                'ssiiss',
                $gamename,
                $background,
                $slotnum,
                $userid,
                $gamespace,
                $rules
            );
            $stmt->execute();
            $stmt->close();
            $gameid = $this->getLastInstertID();
        }

        $this->createSlots($gameid, $slots);

        return $gameid;
    }

    public function submitCriticals($gameid, $criticals, $turn)
    {
        try {

            //print(var_dump($criticals));
            foreach ($criticals as $critical) {
                if ((!$critical->newCrit) && ($critical->turn != $turn))
                    continue;
				//important to use $critical->turn: critical does NOT need to have turn equal to current! 
				//this is importnat for criticals that need to have limited time window yet last longer than 1 turn (go out 1 turn after issuing - so issue must be later)
                $sql = "INSERT INTO `B5CGM`.`tac_critical` VALUES(null, $gameid, " . $critical->shipid . ", " . $critical->systemid . ",'" . $critical->phpclass . "'," . $critical->turn . ",'" . $critical->param . "')";

                $this->update($sql);
            }


        } catch (Exception $e) {
            throw $e;
        }

    }

    public function updateFireOrders($fireOrders)
    {

        $stmt = $this->connection->prepare(
            "UPDATE 
                tac_fireorder  
            SET 
	    	    firingmode = ?,
                needed = ?,
                rolled = ?,
                notes = ?,
                pubnotes = ?,
                shots = ?,
                shotshit = ?,
                intercepted = ?,
                x = ?,
                y = ?
            WHERE
                id = ?
            "
        );

        if ($stmt) {
            foreach ($fireOrders as $fire) {
                $stmt->bind_param(
                    'iiissiiiiii',
                    $fire->firingMode,
                    $fire->needed,
                    $fire->rolled,
                    $fire->notes,
                    $fire->pubnotes,
                    $fire->shots,
                    $fire->shotshit,
                    $fire->intercepted,
                    $fire->x,
                    $fire->y,
                    $fire->id
                );
                $stmt->execute();
            }
            $stmt->close();

        }

    }

    public function submitFireorders($gameid, $fireOrders, $turn, $phase)
    {

        foreach ($fireOrders as $fire) {
            if ($fire->turn != $turn)
                continue;

            if ($fire->type == "ballistic" && $phase != 1)
                continue;

            if ($fire->type != "ballistic" && $phase == 1)
                continue;

            $sql = "INSERT INTO `B5CGM`.`tac_fireorder` VALUES (null, '" . $fire->type . "', " . $fire->shooterid . ", " . $fire->targetid . ", " . $fire->weaponid . ", " . $fire->calledid . ", " . $fire->turn . ", "
                . $fire->firingMode . ", " . $fire->needed . ", " . $fire->rolled . ", $gameid, '" . $fire->notes . "', " . $fire->shotshit . ", " . $fire->shots . ", '" . $fire->pubnotes . "', 0, '" . $fire->x . "', '" . $fire->y . "', '" . $fire->damageclass . "')";

            $this->update($sql);
        }
    }

    public function submitPower($gameid, $turn, $powers)
    {

        try {


            foreach ($powers as $power) {
                if ($power->turn != $turn)
                    continue;

                //$id, $shipid, $systemid, $type, $turn, $amount
                $sql = "INSERT INTO `B5CGM`.`tac_power` VALUES( null, " . $power->shipid . ", " . $gameid . ", " . $power->systemid . ", " . $power->type . ", " . $turn .
                    ", " . $power->amount . ")";

                $this->update($sql);
            }


        } catch (Exception $e) {
            throw $e;
        }

    }

    public function insertSystemData($input)
    {
        $datas = array();
        if (is_array($input))
            $datas = $input;
        else 
            $datas[] = $input;
       
        $stmt = $this->connection->prepare(
            "INSERT INTO  
                tac_systemdata
            VALUES 
            ( 
                ?,?,?,?,?,?
            )
            ON DUPLICATE KEY UPDATE
                data = ?
            "
        );

        if ($stmt) {
            foreach ($datas as $data) {
                $json = $data->toJSON();
                $stmt->bind_param(
                    'iiiisis',
                    $data->systemid,
                    $data->subsystem,
                    $data->gameid,
                    $data->shipid,
                    $json,
                    $data->turn,
                    $json
                );
                $stmt->execute();
            }
            $stmt->close();

        }

    }

    public function updateSystemData($input)
    {
        $this->insertSystemData($input);
        /*
        $datas = array();
        if (is_array($input))
            $datas = $input;
        else 
            $datas[] = $input;
        
        try {
            $stmt = $this->connection->prepare(
                "UPDATE 
                    tac_systemdata
                SET 
                    data = ?
                WHERE 
                    gameid = ? 
                AND 
                    systemid = ?
                AND 
                    shipid = ?
                AND
                    subsystem = ?
                "
            );
            
			if ($stmt)
            {
                foreach ($datas as $data)
                {
                    $json = $data->toJSON();
                    $stmt->bind_param(
                        'siiii', 
                        $json,
                        TacGamedata::$currentGameID,
                        $data->systemid,
                        $data->shipid,
                        $data->subsystem
                    );
                    $stmt->execute();
                }
                $stmt->close();
			}
        }
        catch(Exception $e) {
            throw $e;
        }
         
        */
    }

    public function submitDamages($gameid, $turn, $damages)
    {

        try {


            foreach ($damages as $damage) {

                $des = ($damage->destroyed) ? 1 : 0;
                $fireID = $damage->fireorderid;

                if ($fireID < 0){ //Marcin Sawicki: fire order ID not known at the moment of dealing damage!
                    //read it from database by source, target and weapon ID
                    try{
                        $targetid = $damage->shipid;
                        $shooterid = $damage->shooterid; //additional field
                        $weaponid = $damage->weaponid; //additional field
                        //targetid = -1 if weapon is hex targeted!
                        $sql1 = "SELECT * FROM `B5CGM`.`tac_fireorder` where gameid = $gameid and turn = $turn and shooterid = $shooterid and (targetid = $targetid or targetid = -1) and weaponid = $weaponid";
                        $result = $this->query($sql1);
                        if ($result == null || sizeof($result) == 0){  //nothing, keep -1 as ID
                        }else{
                            $fireID = $result[0]->id;
                        }
                    }catch(Exception $e) { //nothing, keep -1 as ID
                    }
                }

                //$id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields;
                $sql = "INSERT INTO `B5CGM`.`tac_damage` VALUES( null, ".$damage->shipid.", ".$gameid.", ".$damage->systemid.", ".$turn.", ".$damage->damage.
                    ", ".$damage->armour. ", ".$damage->shields.", ".$fireID .", ".$des.", '".$damage->pubnotes."', '".$damage->damageclass."')";


                $this->update($sql);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }


    public function submitDamagesForAdaptiveArmour($gameid, $turn, $damages)
    {
        //debug::log("___submitDamagesForAdaptiveArmour___");
        $obj = array();
        $id;


        usort($damages,
            function ($a, $b) {
                if ($a->fireorderid !== $b->fireorderid) {
                    return $a->fireorderid - $b->fireorderid;
                }
            }
        );


        foreach ($damages as $damage) {
            if (isset($id)) {
                if ($id == $damage->fireorderid) {
                    //debug::log("fireorder id ".$damage->fireorderid." == id ".$id.", continue ");
                    continue;
                }
            }

            if (isset($obj[$damage->damageclass])) {
                $obj[$damage->damageclass] += 1;
                //debug::log($obj[$damage->damageclass]." + 1");
            } else {
                $obj[$damage->damageclass] = 1;
                //debug::log("INIT: ".$obj[$damage->damageclass]." = 1");
            }

            $id = $damage->fireorderid;
            //debug::log("setting id to ".$id);
        }


        foreach ($obj as $key => $value) {
            if ($key != "pulse") {
                if (is_string($key) && strlen($key) > 2) {
                    //debug::log($key." => ".$value);

                    try {
                        $sql = "
                        UPDATE `B5CGM`.`tac_adaptivearmour` 
                        SET `" . $key . "points` = `" . $key . "points` + $value 
                        WHERE gameid = '" . $gameid . "' 
                        AND shipid ='" . $shipid = $damage->shipid . "'";

                        //debug::log($sql);

                        $this->update($sql);
                    } catch (Exception $e) {
                        throw $e;
                    }
                }
            } else {
                //debug::log("PULSE");
            }
        }
    }

    public function submitIniative($gameid, $turn, $ships)
    {

        try {

            foreach ($ships as $ship) {
                $unmodified = $ship->unmodifiedIniative === null ? 'NULL' : $ship->unmodifiedIniative;
                $sql = "INSERT INTO `B5CGM`.`tac_iniative` VALUES($gameid, " . $ship->id . ", $turn, " . $ship->iniative . ", " . $unmodified .")";
                $this->update($sql);
            }
        } catch (Exception $e) {
            throw $e;
        }

    }

    public function updatePlayerStatus($gameid, $userid, $phase, $turn, $slots = null)
    {
        try {
            $sql = "UPDATE `B5CGM`.`tac_playeringame` SET `lastturn` = $turn, `lastphase` = $phase, `lastactivity` = NOW() WHERE"
                . " gameid = $gameid AND playerid = $userid";

            if ($slots) {
                $slots = array_keys($slots);
                $slots = implode(',', $slots);
                $sql .= " AND slot IN ($slots)";
            }

            $this->update($sql);
        } catch (Exception $e) {
            throw $e;
        }

    }

    
    public function setPlayerWaitingStatus($playerid, $gameid, $waiting)
    {
        try {
            if ($stmt = $this->connection->prepare(
                "UPDATE 
                        tac_playeringame
                     SET
                        waiting = ?
                     WHERE 
                        playerid = ? AND gameid = ?
                     "
            )) {
                $stmt->bind_param('iii', $waiting, $playerid, $gameid);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            throw $e;
        }

    }

    public function setPlayersWaitingStatusInGame($gameid, $waiting)
    {
        try {
            if ($stmt = $this->connection->prepare(
                "UPDATE 
                        tac_playeringame
                     SET
                        waiting = ?
                     WHERE 
                        gameid = ?
                     "
            )) {
                $stmt->bind_param('ii', $waiting, $gameid);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            throw $e;
        }

    }

    public function updateGamedata($gamedata)
    {
        try {
            if ($stmt = $this->connection->prepare(
                "UPDATE 
                        tac_game
                     SET
                        turn = ?,
                        phase = ?,
                        activeship = ?,
                        `status` = ?
                     WHERE 
                        id = ?
                     "
            )) {
                $activeShip = json_encode($gamedata->activeship);
                $stmt->bind_param('iissi', $gamedata->turn, $gamedata->phase, $activeShip, $gamedata->status, $gamedata->id);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            throw $e;
        }

    }

    public function submitEW($gameid, $shipid, $EW, $turn)
    {
        try {

            foreach ($EW as $entry) {

                if ($entry->turn != $turn)
                    continue;

                $sql = "INSERT INTO `B5CGM`.`tac_ew` VALUES (null, $gameid, " . $entry->shipid . ", $turn, '" . $entry->type . "', " . $entry->amount . ", " . $entry->targetid . ")";

                $this->insert($sql);
            }

        } catch (Exception $e) {

            throw $e;
        }
    }


    public function updateAdaptiveArmour($gameid, $shipid, $settings)
    {

        try {
            if ($stmt = $this->connection->prepare(
                "UPDATE 
                        tac_adaptivearmour
                     SET
                        particlealloc = ?,
                        laseralloc = ?,
                        molecularalloc = ?,
                        matteralloc = ?,
                        plasmaalloc = ?,
                        electromagneticalloc = ?,
                        antimatteralloc = ?,
                        ionalloc = ?,
                        graviticalloc = ?,
                        ballisticalloc = ?
                     WHERE 
                        gameid = ?
                        AND shipid = ?
                     "
            )) {
                $stmt->bind_param('iiiiiiiiiiii', $settings["particle"][1], $settings["laser"][1], $settings["molecular"][1], $settings["matter"][1], $settings["plasma"][1], $settings["electromagnetic"][1], $settings["antimatter"][1], $settings["ion"][1], $settings["gravitic"][1], $settings["ballistic"][1], $gameid, $shipid);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {

            throw $e;
        }
    }


    public function insertShips($gameid, $ships)
    {
        foreach ($ships as $ship) {
            $move = $ship->movement[0];
            $this->insertMovement($gameid, $ship->id, $move);
        }
    }

    public function insertMovement($gameid, $shipid, $input)
    {
        $moves = array();
        if (is_array($input))
            $moves = $input;
        else
            $moves[] = $input;
        try {
            $stmt = $this->connection->prepare(
                "INSERT INTO  
                    tac_shipmovement
                VALUES 
                ( 
                    null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
                )"
            );

            if ($stmt) {
                foreach ($moves as $move) {
                    $preturn = ($move->preturn) ? 1 : 0;
                    $reqThrust = $move->getReqThrustJSON();
                    $assThrust = $move->getAssThrustJSON();

                    $xOffset = (int)$move->xOffset;
                    $yOffset = (int)$move->yOffset;

                    $stmt->bind_param(
                        'iisiiiiiiiissiii',
                        $shipid,
                        $gameid,
                        $move->type,
                        $move->position->q,
                        $move->position->r,
                        $xOffset,
                        $yOffset,
                        $move->speed,
                        $move->heading,
                        $move->facing,
                        $preturn,
                        $reqThrust,
                        $assThrust,
                        $move->turn,
                        $move->value,
                        $move->at_initiative
                    );
                    $stmt->execute();
                }
                $stmt->close();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function submitMovement($gameid, $shipid, $turn, $movements, $acceptPreturn = false)
    {
        try {

            foreach ($movements as $movement) {

                if ($movement->type == "start" || $movement->turn != $turn)
                    continue;

                $preturn = ($movement->preturn) ? 1 : 0;

                if ($acceptPreturn == false && $preturn)
                    continue;
                $this->insertMovement($gameid, $shipid, $movement);
            }

        } catch (Exception $e) {

            throw $e;
        }
    }

    public function getFirePhaseGames($playerid)
    {

        $games = array();
	/* originally games replaying firing; now when entire history is available for replay, this would be useless
        $sql = "SELECT * FROM `B5CGM`.`tac_game` WHERE phase = 4 AND status = 'ACTIVE'";
	*/
	//replacement: games recently active (including closed ones! - recent conclusion qualifies)
	$sql = "SELECT DISTINCT g.* FROM tac_game g JOIN tac_playeringame p ON p.gameid = g.id
		WHERE DATE_ADD(p.lastactivity, INTERVAL 2 day) >= NOW() ";
        //    debug::log($sql);

        $result = $this->query($sql);

        if ($result == null || sizeof($result) == 0)
            return null;

        foreach ($result as $value) {
            $game = new TacGamedata($value->id, $value->turn, $value->phase, json_decode($value->activeship), $playerid, $value->name, $value->status, $value->points, $value->background, $value->creator, $value->gamespace);
            $games[] = $game;
        }

        return $games;
    }


    public function getTacGamedata($playerid, $gameid, $turn = null)
    {

        if ($gameid <= 0)
            return null;

        /** @var TacGamedata $gamedata */
        $gamedata = $this->getTacGame($gameid, $playerid);
        if ($gamedata == null)
            return null;

        if ($turn === null) {
            $turn = $gamedata->turn;
        }

        $gamedata->slots = $this->getSlotsInGame($gameid);
        $this->getTacShips($gamedata, $turn);
        $gamedata->onConstructed();


        return $gamedata;
    }

    public function getPlayerGames($playerid) {

        $stmt = $this->connection->prepare("select g.id, g.name, pg.waiting from tac_playeringame pg join tac_game g on pg.gameid = g.id where g.status = 'ACTIVE' AND pg.playerid = ?");
        $games = [];

        if ($stmt) {
            $stmt->bind_param('i', $playerid);
            $stmt->bind_result($id, $gameName, $waiting);
            $stmt->execute();
            while ($stmt->fetch()) {
                $games[] = ["id" => $id, "name" => $gameName, "waiting" => $waiting, "status" => "ACTIVE"];
            }
            $stmt->close();
        }
        return $games;

    }

    public function getLobbyGames() {

        //$stmt = $this->connection->prepare("select g.id as parentGameId, g.name, g.slots, (select count(gameid) from tac_playeringame where gameid = parentGameId ) as numberOfPlayers from tac_game g WHERE  g.status = 'LOBBY';");
	//above always returns playerCount = number of slots, let's try different approach (Marcin Sawicki):
	$stmt = $this->connection->prepare("select g.id as parentGameId, g.name, g.slots, (select count(distinct playerid) from tac_playeringame where gameid = parentGameId and playerid > 0 ) as numberOfPlayers from tac_game g WHERE  g.status = 'LOBBY';");    
        $games = [];

        if ($stmt) {
            $stmt->bind_result($id, $gameName, $slots, $playerCount);
            $stmt->execute();
            while ($stmt->fetch()) {
                $games[] = ["id" => $id, "name" => $gameName, "slots" => $slots, "playerCount" => $playerCount, "status" => "LOBBY"];
            }
            $stmt->close();
        }
        return $games;

    }

    public function getTacGame($gameid, $playerid)
    {
         $sql = "SELECT * FROM `B5CGM`.`tac_game` where id = $gameid";
    

        $games = array();

        try {
            $result = $this->query($sql);

            if ($result == null || sizeof($result) == 0)
                return null;

            foreach ($result as $value) {
                $game = new TacGamedata($value->id, $value->turn, $value->phase, json_decode($value->activeship), $playerid, $value->name, $value->status, $value->points, $value->background, $value->creator, $value->gamespace, json_decode($value->rules, true));
                $games[] = $game;
            }

            //$this->close();

        } catch (Exception $e) {
            throw $e;
        }

        if ($gameid > 0) {
            return $games[0];
        } else {
            return $games;
        }

    }

    public function getSlotsInGame($gameid)
    {

        $slots = array();

        $stmt = $this->connection->prepare("
            SELECT 
                playerid, slot, teamid, lastturn, lastphase, name, points, depx, depy, deptype, depwidth, depheight, depavailable, p.username
            FROM 
                tac_playeringame pg
            LEFT JOIN 
                player p on p.id = pg.playerid
            WHERE 
                gameid = ?
        ");

        if ($stmt) {
            $stmt->bind_param('i', $gameid);
            $stmt->bind_result($playerid, $slot, $teamid, $lastturn, $lastphase, $name, $points, $depx, $depy, $deptype, $depwidth, $depheight, $depavailable, $username);
            $stmt->execute();
            while ($stmt->fetch()) {
                $slots[$slot] = new PlayerSlot($playerid, $slot, $teamid, $lastturn, $lastphase, $name, $points, $depx, $depy, $deptype, $depwidth, $depheight, $depavailable, $username);
            }
            $stmt->close();
        }
        return $slots;
    }

    public function getSlotById($slotid, $gameid)
    {
        $slot = null;

        $stmt = $this->connection->prepare("
            SELECT 
                playerid, slot, teamid, lastturn, lastphase, name, points, depx, depy, deptype, depwidth, depheight, depavailable, p.username
            FROM 
                tac_playeringame pg
            LEFT JOIN 
                player p on p.id = pg.playerid
            WHERE 
                gameid = ?
            AND
                slot = ?
        ");

        if ($stmt) {
            $stmt->bind_param('ii', $gameid, $slotid);
            $stmt->bind_result($playerid, $slot, $teamid, $lastturn, $lastphase, $name, $points, $depx, $depy, $deptype, $depwidth, $depheight, $depavailable, $username);
            $stmt->execute();
            while ($stmt->fetch()) {
                $slot = new PlayerSlot($playerid, $slot, $teamid, $lastturn, $lastphase, $name, $points, $depx, $depy, $deptype, $depwidth, $depheight, $depavailable, $username);
            }
            $stmt->close();
        }
        return $slot;
    }

    public function getShipByIdFromDB($id)
    {
        $ship = null;

        $stmt = $this->connection->prepare(
            "SELECT
                id, playerid, name, phpclass, slot
            FROM
                tac_ship 
            WHERE
                id = ?
            "
        );

        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->bind_result($id, $playerid, $name, $phpclass, $slot);
            $stmt->execute();
            while ($stmt->fetch()) {
                $ship = new $phpclass($id, $playerid, $name, $slot);
            }
            $stmt->close();
        }

        return $ship;
    }

    public function getTacShips($gamedata, $turn, $allData = true)
    {

        //$starttime = time();
        $ships = array();

        $stmt = $this->connection->prepare(
            "SELECT
                id, playerid, name, phpclass, slot
            FROM
                tac_ship 
            WHERE
                tacgameid = ?
            "
        );

        if ($stmt) {
            $stmt->bind_param('i', $gamedata->id);
            $stmt->bind_result($id, $playerid, $name, $phpclass, $slot);
            $stmt->execute();
            while ($stmt->fetch()) {
                $ship = new $phpclass($id, $playerid, $name, $slot);
                /*    if ($ship instanceof FighterFlight && $ship->superheavy === false){
                        debug::log("backwards adjust");
                        $ship->flightSize = 6;
                        $ship->populate();
                    }
                */
                $ship->team = $gamedata->slots[$slot]->team;
                $ships[] = $ship;
            }
            $stmt->close();
        }

        $gamedata->setShips($ships);

        if ($allData) {
            $this->getFlightSize($gamedata);
            $this->flightSizeFix($ships);
            $this->getAdaptiveArmourSettings($gamedata);
            $this->getIniativeForShips($gamedata, $turn);
            $this->getMovesForShips($gamedata, $turn);
            $this->getEWForShips($gamedata, $turn);
            $this->getPowerForShips($gamedata, $turn);
            $this->getCriticalsForShips($gamedata, $turn);
            $this->getDamageForShips($gamedata, $turn);
            $this->getFireOrdersForShips($gamedata, $turn);
            $this->getSystemDataForShips($gamedata, $turn);
        }

        //$endtime = time();
        //Debug::log("GETTING SHIPS - GAME: $gamedata->id Fetching gamedata took " . ($endtime - $starttime) . " seconds.");


    }

    public function getAdaptiveArmourSettings($gamedata)
    {
        $stmt = $this->connection->prepare(
            "SELECT 
                shipid, particlepoints, particlealloc, laserpoints, laseralloc, molecularpoints, molecularalloc, matterpoints, matteralloc, plasmapoints, plasmaalloc, electromagneticpoints, electromagneticalloc, antimatterpoints, antimatteralloc, ionpoints, ionalloc, graviticpoints, graviticalloc, ballisticpoints, ballisticalloc
            FROM 
                tac_adaptivearmour
            WHERE 
                gameid = ?"
        );

        if ($stmt) {
            $stmt->bind_param('i', $gamedata->id);
            $stmt->bind_result($shipid, $particlepoints, $particlealloc, $laserpoints, $laseralloc, $molecularpoints, $molecularalloc, $matterpoints, $matteralloc, $plasmapoints, $plasmaalloc, $electromagneticpoints, $electromagneticalloc, $antimatterpoints, $antimatteralloc, $ionpoints, $ionalloc, $graviticpoints, $graviticalloc, $ballisticpoints, $ballisticalloc);
            $stmt->execute();


            while ($stmt->fetch()) {
                $ship = $gamedata->getShipById($shipid);

                $ship->armourSettings["particle"] = array($particlepoints, $particlealloc);
                $ship->armourSettings["laser"] = array($laserpoints, $laseralloc);
                $ship->armourSettings["molecular"] = array($molecularpoints, $molecularalloc);
                $ship->armourSettings["matter"] = array($matterpoints, $matteralloc);
                $ship->armourSettings["plasma"] = array($plasmapoints, $plasmaalloc);
                $ship->armourSettings["electromagnetic"] = array($electromagneticpoints, $electromagneticalloc);
                $ship->armourSettings["antimatter"] = array($antimatterpoints, $antimatteralloc);
                $ship->armourSettings["ion"] = array($ionpoints, $ionalloc);
                $ship->armourSettings["gravitic"] = array($graviticpoints, $graviticalloc);
                $ship->armourSettings["ballistic"] = array($ballisticpoints, $ballisticalloc);
            }

            $stmt->close();
        }
    }


    public function getFlightSize($gamedata)
    {
        $stmt = $this->connection->prepare(
            "SELECT 
                shipid, flightsize
            FROM 
                tac_flightsize
            WHERE 
                gameid = ?"
        );

        if ($stmt) {
            $stmt->bind_param('i', $gamedata->id);
            $stmt->bind_result($shipid, $flightsize);
            $stmt->execute();
            /*    $stmt->store_result();

                $num = $stmt->num_rows;

                if ($num === 0){
                    for ($))
                }
    */
            while ($stmt->fetch()) {
                $flight = $gamedata->getShipById($shipid);
                $flight->flightSize = $flightsize;
                $flight->populate();
            }

            $stmt->close();
        }
    }

    public function flightSizeFix($ships)
    {
        foreach ($ships as $ship) {
            if ($ship instanceof FighterFlight && !$ship->superheavy) {
                if ($ship->flightSize == 1) {
                    $ship->flightSize = 6;
                    $ship->populate();
                }
            }
        }
    }


    private function getIniativeForShips($gamedata, $fetchTurn)
    {


        $stmt = $this->connection->prepare(
            "SELECT
                iniative, unmodified_iniative as unmodified, shipid
            FROM
                tac_iniative 
            WHERE
                gameid = ?
            AND
                turn = ?
            "
        );

        if ($stmt) {
            $stmt->bind_param('ii', $gamedata->id, $fetchTurn);
            $stmt->bind_result($iniative, $unmodified, $shipid);
            $stmt->execute();

            while ($stmt->fetch()) {
                $ship = $gamedata->getShipById($shipid);
                $ship->iniative = $iniative;
                $ship->unmodifiedIniative = $unmodified;
            }

            $stmt->close();
        }


    }

    private function getMovesForShips($gamedata, $fetchTurn)
    {

        $stmt = $this->connection->prepare("
            SELECT 
                id, shipid, type, x, y, xOffset, yOffset, speed, heading, facing, preturn, turn, value, requiredthrust, assignedthrust, at_initiative
            FROM 
                tac_shipmovement
            WHERE
                gameid = ? AND (turn = 1 OR turn = ? OR turn = ?) 
            ORDER BY
                shipid ASC, id ASC
        ");

        if ($stmt) {
            $lastTurn = $fetchTurn - 1;
            $stmt->bind_param('iii', $gamedata->id, $lastTurn, $fetchTurn);
            $stmt->bind_result($id, $shipid, $type, $x, $y, $xOffset, $yOffset, $speed, $heading, $facing, $preturn, $turn, $value, $requiredthrust, $assignedthrust, $at_initiative);
            $stmt->execute();

            while ($stmt->fetch()) {

                $move = new MovementOrder($id, $type, new OffsetCoordinate($x, $y), $xOffset, $yOffset, $speed, $heading, $facing, $preturn, $turn, $value, $at_initiative);
                $move->setReqThrustJSON($requiredthrust);
                $move->setAssThrustJSON($assignedthrust);
                $gamedata->getShipById($shipid)->setMovement($move);

            }

            $stmt->close();
        }
    }

    private function getEWForShips(TacGamedata $gamedata, $fetchTurn)
    {

        $stmt = $this->connection->prepare(
            "SELECT 
                id, shipid, turn, type, amount, targetid
            FROM 
                tac_ew 
            WHERE 
                gameid = ? AND (turn = ? OR turn = ?)
            ORDER BY
                id ASC
            "
        );


        if ($stmt) {
            $lastTurn = $fetchTurn - 1;

            $stmt->bind_param('iii', $gamedata->id, $lastTurn, $fetchTurn);
            $stmt->bind_result($id, $shipid, $turn, $type, $amount, $targetid);
            $stmt->execute();
            while ($stmt->fetch()) {
                $gamedata->getShipById($shipid)->setEW(
                    new EWentry($id, $shipid, $turn, $type, $amount, $targetid)
                );
            }

        }


    }

    /* original version */
    private function getDamageForShips($gamedata, $fetchTurn)
    {
        $damageStmt = $this->connection->prepare(
            "SELECT 
                id, shipid, gameid, turn, systemid, damage, armour, shields, fireorderid, destroyed, pubnotes, damageclass 
            FROM
                tac_damage
            WHERE 
                gameid = ? AND turn <= ?
            "
        );

        if ($damageStmt) {
            $damageStmt->bind_param('ii', $gamedata->id, $fetchTurn);
            $damageStmt->bind_result($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $pubnotes, $damageclass);
            $damageStmt->execute();
            while ($damageStmt->fetch()) {
                $gamedata->getShipById($shipid)->getSystemById($systemid)->setDamage(
                    new DamageEntry($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $pubnotes, $damageclass)
                );
            }
            $damageStmt->close();
        }
    }

    /*modified version - still with problems*/ /*
    private function getDamageForShips($gamedata)
    {
	    //Marcin Sawicki: combine old damages into one damage entry; ignore damage entries if primary structure is destroyed
        $damageStmt = $this->connection->prepare(
            "SELECT 
                id, shipid, gameid, turn, systemid, damage, armour, shields, fireorderid, destroyed, pubnotes, damageclass 
            FROM
                tac_damage
            WHERE 
                gameid = ? 
		ORDER BY destroyed DESC, shipid ASC, systemid DESC, turn DESC
            "
        );
        
        if ($damageStmt)
        {
            $damageStmt->bind_param('i', $gamedata->id);
            $damageStmt->bind_result($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $pubnotes, $damageclass );
            $damageStmt->execute();
		$waitingEntry = false;
		    $combinedDamage = 0;
		    $combinedArmour = 0;
		    $combinedShields = 0;
		$prevShip = -1;
		$prevSystem = -1;
            while ($damageStmt->fetch())
            {
		    $ship = $gamedata->getShipById($shipid);
		    if($ship->isDestroyed($gamedata->turn-2) ){
			    continue; //don't load further data for a ship that's already determined to be destroyed (that's why killing blows go first)
		    }
		       
		    if( ($turn >= $gamedata->turn -1) || ($destroyed == true)){ //recent turn OR killing blow - always load full damage! do not touch Combined data though
			   $ship->getSystemById($systemid)->setDamage(
			      new DamageEntry($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $pubnotes, $damageclass )
			   ); 
			    continue;
		    }
		    
		    if( ( ($prevShip!=$shipid) || ($prevSystem!=$systemid)  )
		       && ($prevShip != -1) ) { //current damage is for different system than previous, and previous data exists!
				$gamedata->getShipById($prevShip)->getSystemById($prevSystem)->setDamage(
				    new DamageEntry($id, $prevShip, $gameid, $turn, $prevSystem, $combinedDamage, $combinedArmour, $combinedShields, $fireorderid, $destroyed, $pubnotes, $damageclass )
				);
			    	//reset sums
				    $combinedDamage = 0;
				    $combinedArmour = 0;
				    $combinedShields = 0;
		    }
		    //accumulate damage data with current entry
		    $prevShip = $shipid;
		    $prevSystem =  $systemid;
		    $waitingEntry = true;
		    $combinedDamage += $damage;
		    $combinedArmour += $armour;
		    $combinedShields += $shields;
            }
	       if($waitingEntry == true ){ //create any entry that got out of loop - if any!
			$gamedata->getShipById($prevShip)->getSystemById($prevSystem)->setDamage(
			    new DamageEntry($id, $prevShip, $gameid, $turn, $prevSystem, $combinedDamage, $combinedArmour, $combinedShields, $fireorderid, $destroyed, $pubnotes, $damageclass )
			);
	       }
            $damageStmt->close();
        }
    }
    */

    private function getCriticalsForShips($gamedata, $fetchTurn)
    {
        $criticalStmt = $this->connection->prepare(
            "SELECT 
                id, shipid, systemid, type, turn, param 
            FROM 
                tac_critical
            WHERE 
                gameid = ? AND turn <= ?
            "
        );

        if ($criticalStmt) {
            $criticalStmt->bind_param('ii', $gamedata->id, $fetchTurn);
            $criticalStmt->bind_result($id, $shipid, $systemid, $type, $turn, $param);
            $criticalStmt->execute();
            while ($criticalStmt->fetch()) {
                $gamedata->getShipById($shipid)->getSystemById($systemid)->setCritical(
                    new $type($id, $shipid, $systemid, $type, $turn, $param),
                    $gamedata->turn
                );
            }
            $criticalStmt->close();
        }


    }

    private function getPowerForShips($gamedata, $fetchTurn)
    {
        $powerStmt = $this->connection->prepare(
            "SELECT
                id, shipid, systemid, type, turn, amount 
            FROM
                tac_power
            WHERE 
                gameid = ? AND (turn = ? OR turn = ?)
            "
        );

        if ($powerStmt) {
            $lastTurn = $fetchTurn - 1;
            $powerStmt->bind_param('iii', $gamedata->id, $lastTurn, $fetchTurn);
            $powerStmt->bind_result($id, $shipid, $systemid, $type, $turn, $amount);
            $powerStmt->execute();
            while ($powerStmt->fetch()) {
                $gamedata->getShipById($shipid)->getSystemById($systemid)->setPower(
                    new PowerManagementEntry($id, $shipid, $systemid, $type, $turn, $amount)
                );
            }
            $powerStmt->close();
        }


    }


    public function getSystemDataForShips(TacGamedata $gamedata, $fetchTurn)
    {
        $stmt = $this->connection->prepare(
        "SELECT 
                (SELECT data FROM tac_systemdata WHERE systemid = t.systemid AND shipid = t.shipid AND gameid = ? AND turn <= ? ORDER BY turn DESC limit 1) AS data, shipid, systemid, subsystem
            FROM
                tac_systemdata t
            WHERE 
                gameid = ?  
            GROUP BY 
                systemid, subsystem, gameid, shipid
          "
        );

        //select shipid, systemid, max(turn) as maxturn, (select data from tac_systemdata where systemid = t.systemid and shipid = t.shipid and turn <= 1 order by turn desc limit 1) as data from tac_systemdata t group by systemid, subsystem, gameid, shipid;

        if ($stmt) {
            $stmt->bind_param('iii', $gamedata->id, $fetchTurn, $gamedata->id);
            $stmt->execute();
            $stmt->bind_result(
                $data,
                $shipid,
                $systemid,
                $subsystem
            );

            while ($stmt->fetch()) {
                //Debug::log("get ship by id '$shipId'\n");
                $gamedata->getShipById($shipid)->getSystemById($systemid)->setSystemData($data, $subsystem);
            }
            $stmt->close();
        }

        // Get ammo info
        $stmt = $this->connection->prepare(
            "SELECT 
                shipid, systemid, firingmode, (select ammo from tac_ammo where shipid = t.shipid and systemid = t.systemid and firingmode = t.firingmode and gameid = ? and turn <= ? order by turn desc limit 1)
            FROM 
                tac_ammo t
            WHERE 
                gameid = ?
            GROUP BY
              shipid, systemid, firingmode
            "

        );

        //select shipid, systemid, firingmode, (select ammo from tac_ammo where shipid = t.shipid and systemid = t.systemid and firingmode = t.firingmode and turn <= 1 order by turn desc limit 1) as ammo from tac_ammo t group by shipid, systemid, firingmode;


        if ($stmt) {
            $stmt->bind_param('iii', $gamedata->id, $fetchTurn, $gamedata->id);
            $stmt->execute();
            $stmt->bind_result(
                $shipid,
                $systemid,
                $firingmode,
                $ammo
            );

            while ($stmt->fetch()) {
                // This is a dual/duoweapon or a fightersystem
                $gamedata->getShipById($shipid)->getSystemById($systemid)->setAmmo($firingmode, $ammo);
            }
            $stmt->close();
        }
    }

    public function getFireOrdersForShips($gamedata, $fetchTurn)
    {
        $stmt = $this->connection->prepare(
            "SELECT 
                *
            FROM 
                tac_fireorder
            WHERE 
                gameid = ? AND (turn = ? OR turn = ?)"
        );

        if ($stmt) {
            $lastTurn = $fetchTurn;
            $stmt->bind_param('iii', $gamedata->id, $fetchTurn, $lastTurn);
            $stmt->execute();
            $stmt->bind_result(
                $id,
                $type,
                $shooterid,
                $targetid,
                $weaponid,
                $calledid,
                $turn,
                $firingMode,
                $needed,
                $rolled,
                $gameid,
                $notes,
                $shotshit,
                $shots,
                $pubnotes,
                $intercepted,
                $x,
                $y,
                $damageclass
            );

            while ($stmt->fetch()) {
                $entry = new FireOrder(
                    $id, $type, $shooterid, $targetid,
                    $weaponid, $calledid, $turn, $firingMode, $needed,
                    $rolled, $shots, $shotshit, $intercepted, $x, $y, $damageclass
                );

                $entry->notes = $notes;
                $entry->pubnotes = $pubnotes;

                $gamedata->getShipById($shooterid)->getSystemById($weaponid)->setFireOrder($entry);
            }
            $stmt->close();
        }
    }

    public function isNewGamedata($gameid, $turn, $phase, $activeship)
    {
        try {
            if ($stmt = $this->connection->prepare("
                SELECT 
                    turn, phase, activeship, status
                FROM 
                    tac_game
                WHERE 
                    id = ? 
                
                ")) {

                $stmt->bind_param('i', $gameid);
                $stmt->execute();

                $stmt->bind_result($dbturn, $dbphase, $dbactiveship, $dbstatus);
                $stmt->fetch();

                $stmt->close();

                if ($dbstatus === "LOBBY")
                    return true;

                if ($dbphase !== $phase || $dbturn !== $turn || json_decode($dbactiveship) !== $activeship)
                    return true;

            }

            return false;

        } catch (Exception $e) {
            throw $e;
        }

    }

    public function registerPlayer($username, $password)
    {
        $username = htmlspecialchars($username);
        $username = $this->DBEscape($username);

        //for password - do a similar escape?...
        $password = htmlspecialchars($password);
        $password = $this->DBEscape($password);

        $sql = "SELECT * FROM player WHERE username LIKE '$username'";
        if ($this->found($sql)) {
            return false;
        }

        if ($stmt = $this->connection->prepare("
            INSERT INTO 
                player
            VALUES
            (
                null,
                ?,
                password(?),
                1
            );
            ")) {
            $stmt->bind_param('ss', $username, $password);
            $stmt->execute();
            $stmt->close();
        }

        return true;
    }


    public function changePassword($username, $passwordold, $passwordnew) //change password for a given account
    {
        $username = htmlspecialchars($username);
        $username = $this->DBEscape($username);

        //for password - do a similar escape?...
        $passwordold = htmlspecialchars($passwordold);
        $passwordold = $this->DBEscape($passwordold);
        $passwordnew = htmlspecialchars($passwordnew);
        $passwordnew = $this->DBEscape($passwordnew);

        $sql = "SELECT * FROM player WHERE username LIKE '$username' and password LIKE password('$passwordold')";
        if (!$this->found($sql)) {
            return false;
        }

        if ($stmt = $this->connection->prepare("
            UPDATE 
                player
            SET 
            	password = password(?)
            WHERE
            	username = ?
            ;
            ")) {
            $stmt->bind_param('ss', $passwordnew, $username);
            $stmt->execute();
            $stmt->close();
        }

        return true;
    }


    public function authenticatePlayer($username, $password)
    {

        $id = false;
        try {
            if ($stmt = $this->connection->prepare(
                "SELECT id, accesslevel FROM player where username = ? and password = password(?)")) {

                $stmt->bind_param('ss', $username, $password);
                $stmt->execute();
                $stmt->bind_result($id, $access);
                $stmt->fetch();

                /* close statement */
                $stmt->close();
            }

            if (!$id)
                return false;

        } catch (Exception $e) {
            throw $e;
        }

        return array('id' => $id, 'access' => $access);
    }

    public function releaseGameSubmitLock($gameid)
    {
        try {
            if ($stmt = $this->connection->prepare(
                "UPDATE 
                    tac_game 
                SET
                    submitLock = null
                WHERE 
                    id = ?
                "
            )) {

                $stmt->bind_param('i', $gameid);
                $stmt->execute();

                $stmt->close();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function updateGameStatus($gameid, $status)
    {
        try {
            if ($stmt = $this->connection->prepare(
                "UPDATE 
                    tac_game 
                SET
                    status = ?
                WHERE 
                    id = ?
                "
            )) {
                $stmt->bind_param('si', $status, $gameid);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function releasePlayerSubmitLock($gameid, $playerid)
    {
        try {
            if ($stmt = $this->connection->prepare(
                "UPDATE 
                    tac_playeringame 
                SET
                    submitLock = null
                WHERE 
                    gameid = ?
                AND
                    playerid = ?
                "
            )) {
                $stmt->bind_param('ii', $gameid, $playerid);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function getGameSubmitLock($gameid)
    {
        $result = false;
        try {
            if ($stmt = $this->connection->prepare(
                "UPDATE 
                    tac_game
                SET
                    submitLock = now()
                WHERE 
                    id = ?
                AND
                (  
                    DATE_ADD(submitLock, INTERVAL 15 MINUTE) < NOW()
                OR
                    submitLock IS NULL
                )"
            )) {

                $stmt->bind_param('i', $gameid);
                $stmt->execute();

                if ($stmt->affected_rows == 1)
                    $result = true;

                /* close statement */
                $stmt->close();
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $result;
    }

    public function getPlayerSubmitLock($gameid, $playerid)
    {
        $result = false;
        try {
            if ($stmt = $this->connection->prepare(
                "UPDATE 
                    tac_playeringame
                SET
                    submitLock = now()
                WHERE 
                    gameid = ?
                AND
                    playerid = ?
                AND
                (  
                    DATE_ADD(submitLock, INTERVAL 15 MINUTE) < NOW()
                OR
                    submitLock IS NULL
                )"
            )) {

                $stmt->bind_param('ii', $gameid, $playerid);
                $stmt->execute();

                if ($stmt->affected_rows > 0)
                    $result = true;

                /* close statement */
                $stmt->close();
            }
        } catch (Exception $e) {
            throw $e;
        }

        return $result;
    }

    public function checkIfPhaseReady($gameid)
    {
        try {
            $stmt = $this->connection->prepare(
                "SELECT 
                    g.id, g.slots
                FROM 
                    tac_playeringame p
                INNER JOIN tac_game g on g.id = p.gameid
                WHERE 
                    p.lastphase = g.phase
                AND 
                    p.lastturn = g.turn
                AND 
                    g.id = ?
                AND
                    g.phase != 2
                GROUP BY p.gameid
                HAVING 
                    count(p.playerid) = g.slots"
            );

            if ($stmt) {
                $stmt->bind_param('i', $gameid);
                $stmt->execute();
                $stmt->bind_result($id, $slots);
                $stmt->fetch();
                $stmt->close();

                if ($id) {
                    return true;
                }


            }
        } catch (Exception $e) {
            throw $e;
        }

        return false;

    }

    public function getGamesToBeDeleted()
    {
        $ids = array();
        $stmt = $this->connection->prepare("
            SELECT 
                g.id
            FROM 
                tac_game g
            JOIN 
                tac_playeringame p
            ON
                p.gameid = g.id
            WHERE
                DATE_ADD(p.lastactivity, INTERVAL 2 MONTH) < NOW()
            OR
                (DATE_ADD(p.lastactivity, INTERVAL 3 DAY) < NOW() 
                AND
                g.status = 'LOBBY')

        ");

        if ($stmt) {
            $stmt->bind_result($id);
            $stmt->execute();
            while ($stmt->fetch()) {
                $ids[] = $id;
            }
            $stmt->close();
        }

        return $ids;

    }

    public function deleteGames($ids)
    {
        try {
            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_game
                WHERE
                    id = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);

            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_playeringame
                WHERE
                    gameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);

            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_critical
                WHERE
                    gameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);

            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_ew
                WHERE
                    gameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);

            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_damage
                WHERE
                    gameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);

            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_fireorder
                WHERE
                    gameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);

            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_iniative
                WHERE
                    gameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);

            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_systemdata
                WHERE
                    gameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);

            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_ship
                WHERE
                    tacgameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);

            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_shipmovement
                WHERE
                    gameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);

            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_power
                WHERE
                    gameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);

            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_ammo
                WHERE
                    gameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);

            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_flightsize
                WHERE
                    gameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);
        } catch (Exception $e) {
            throw $e;
        }
    }

    private function executeGameDeleteStatement($stmt, $ids)
    {
        if ($stmt) {
            foreach ($ids as $id) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
            }
            $stmt->close();
        }
    }

    public function getLastTimeChatChecked($userid, $gameid)
    {
        $lastTime = null;

        $stmt = $this->connection->prepare("
            SELECT 
                last_checked
            FROM
                player_chat
            WHERE
                playerid = ?
            AND 
                gameid = ?
        ");

        if ($stmt) {
            $stmt->bind_param('ii', $userid, $gameid);
            $stmt->bind_result($lastTimeChecked);
            $stmt->execute();

            $stmt->fetch();

            $lastTime = $lastTimeChecked;

            $stmt->close();
        }

        return $lastTime;
    }

    public function setLastTimeChatChecked($userid, $gameid)
    {
        // First check if there is already an entry for this game and player
        $stmt = $this->connection->prepare("
            SELECT 
                last_checked
            FROM
                player_chat
            WHERE
                playerid = ?
            AND 
                gameid = ?
        ");

        if ($stmt) {
            $stmt->bind_param('ii', $userid, $gameid);
            $stmt->bind_result($time);
            $stmt->execute();
            $stmt->fetch();

            $stmt->close();
        }

        // Either update or insert depending on whether there is already
        // an entry or not.
        if ($time != "") {
            $stmt = $this->connection->prepare("
                UPDATE 
                    player_chat
                SET
                    last_checked = now()
                WHERE
                    playerid = ?
                AND 
                    gameid = ?
            ");
        } else {
            $stmt = $this->connection->prepare("
                INSERT INTO
                    player_chat
                VALUES
                (
                    ?,
                    ?,
                    now()
                )
            ");
        }

        if ($stmt) {
            $stmt->bind_param('ii', $userid, $gameid);
            $stmt->execute();

            $stmt->close();
        }
    }

    public function submitChatMessage($userid, $message, $gameid = 0)
    {

        $stmt = $this->connection->prepare("
                INSERT INTO 
                    chat
                VALUES
                (
                    null,
                    ?,
                    (SELECT username FROM player WHERE id = ?),
                    ?,
                    now(),
                    ?
                )

            ");

        if ($stmt) {
            $stmt->bind_param('iiis', $userid, $userid, $gameid, $message);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function getChatMessages($lastid, $gameid = 0)
    {
        $messages = array();
        $stmt = $this->connection->prepare("
            SELECT 
                id, userid, username, gameid, message, time
            FROM
                chat
            WHERE
                gameid = ?
            AND 
                id > ?
            ORDER BY id DESC
            LIMIT 50;
        ");

        if ($stmt) {
            $stmt->bind_param('ii', $gameid, $lastid);
            $stmt->bind_result($id, $userid, $username, $gameid, $message, $time);
            $stmt->execute();
            while ($stmt->fetch()) {
                $messages[$id] =
                    new ChatMessage($id, $userid, $username, $gameid, $message, $time);
            }
            $stmt->close();
        }

        ksort($messages);
        return $messages;
    }

    public function getHelpMessage($gamehelpmessagelocation)
    {
        $message = "";
        $helpimg = "";
        $nextpage = 0;
        try {
            if ($stmt = $this->connection->prepare("SELECT message,HelpImage,nextpageid FROM fx_helpmessages WHERE HelpLocation = ?")) {
                $stmt->bind_param('s', $gamehelpmessagelocation);
                $stmt->bind_result($message, $helpimg, $nextpage);
                $stmt->execute();
                $stmt->fetch();
                $stmt->close();
            }
        } catch (Exception $e) {
            throw $e;
        }
        return array('message' => $message, 'helpimg' => $helpimg, 'nextpageid' => $nextpage);
    }

    public function deleteOldChatMessages()
    {
        $stmt = $this->connection->prepare("
            DELETE FROM
                chat
            WHERE
                DATE_ADD(time, INTERVAL 3 DAY) < NOW()    
        ");

        if ($stmt) {
            $stmt->execute();
            $stmt->close();
        }

    }

    //UTILS

    public function chekcIfTableExists($name, $close = true)
    {
        $sql = "show tables like '$name'";

        try {
            $result = $this->query($sql);

            if ($result == null || sizeof($result) == 0)
                return false;


        } catch (Exception $e) {
            throw $e;
        }

        return true;

    }

    public function createDatabase($sql)
    {
        $a = explode(";", $sql);
        try {
            foreach ($a as $line) {
                $line = trim($line);
                if (empty($line))
                    continue;

                $this->update(trim($line));
            }

        } catch (Exception $e) {
            throw $e;
        }
    }
}

