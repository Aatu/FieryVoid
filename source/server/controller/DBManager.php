<?php 
mysqli_report(MYSQLI_REPORT_ERROR);

class DBManager {

    private $connection = null;
    
    function __construct($host, $port = 3306, $database, $username, $password) {
        $this->id = uniqid();
        if ($this->connection !== null)
            return $this->connection;
        
        if (!$this->connection = mysqli_connect($host, $username, $password, $database, $port))
            throw new CustomException(300,"DBManager:Construct, connection failed: ".mysqli_connect_error(), mysqli_connect_errno(), null);
            
        if (!mysqli_select_db($this->connection, $database ))
            throw new CustomException(300,"DBManager:Construct, connection failed: ".mysqli_error($this->connection), mysqli_errno(), null);
            
        mysqli_set_charset($this->connection, 'utf8');
    }
    
    private function DBEscape($string) {
            
        return mysqli_real_escape_string($this->connection, (String)$string);
    }
    
    
    public function __destruct() {
        $this->close();
    }
       
    private function query($sql) {

    
        if (!$this->connection)
            throw new Exception("DBManager:query, connection failed");
            
        if (!$answer = mysqli_query($this->connection, $sql)){
            throw new Exception("DBManager:query, SQL error: ".mysql_error($this->connection)."\n sql: $sql error:", mysql_errno($this->connection));
        }
            
        $result = array();
				
		while ($row = mysqli_fetch_object($answer)) {
			$result[] = $row;
		}
		
        return $result;
    }
    
    private function insert($sql) {

    
        if (!$this->connection)
            throw new exception("DBManager:insert, connection failed");
            
        if (!$answer = mysqli_query($this->connection, $sql))
            throw new exception("DBManager:insert, SQL error: ".mysqli_error($this->connection)."\n sql: $sql". mysqli_errno($this->connection));
            
        return $this->getLastInstertID();

        
    }
    
    private function getLastInstertID()
    {
        $sql = "select LAST_INSERT_ID() as id";
        
        if (!$answer = mysqli_query($this->connection, $sql))
           throw new exception("DBManager:insert, SQL (getting the id) error: ".mysqli_error($this->connection)."\n sql: $sql", mysqli_errno($this->connection));

            
        while ($row = mysqli_fetch_object($answer)) {
            return $row->id;
        }
        
        return null;
    }
    
    public function update($sql) {

    
        if (!$this->connection)
            throw new exception("DBManager:update, connection failed");
            
        if ( ! $answer = mysqli_query($this->connection, $sql)){
            throw new exception("DBManager:update, SQL error: ".mysqli_error($this->connection)."\n sql: $sql", mysqli_errno($this->connection));
        }

            
    }
	
	private function found($sql){
		$result = $this->query($sql);
		
		if ($result != null && sizeof($result)>0)
			return true;
		
		return false;
	}
    
    public function startTransaction(){
		//mysqlii_query("SET AUTOCOMMIT=0", $this->connection);
		//mysqlii_query("START TRANSACTION", $this->connection);
        mysqli_autocommit($this->connection, FALSE);
    }
    
    public function endTransaction($rollback = false){
        if ($rollback == true){
            mysqli_rollback($this->connection); 
        }else{
            mysqli_commit($this->connection);
        }
        mysqli_autocommit($this->connection, TRUE);
        
    }
    
    public function close() {
		mysqli_close($this->connection);
    }
	
	
	public function submitShip($gameid, $ship, $userid){
	
		try{
			
			$sql = "INSERT INTO `B5CGM`.`tac_ship` VALUES(null, $userid, $gameid, '".$this->DBEscape($ship->name)."', '".$ship->phpclass."', 0, 0, 0, 0, 0, $ship->slot)";
            Debug::log($sql);
			$id = $this->insert($sql);
			
			//$sql = "INSERT INTO `B5CGM`.`tac_iniative` VALUES($gameid, $id, 0, 0)";
            //$this->insert($sql);			
			
		}catch(Exception $e) {
			$this->endTransaction(true);
            throw $e;
        }
	}
    
    public function deleteEmptyGames()
    {
        $ids = array();
        $stmt = $this->connection->prepare("
            SELECT 
                gameid, playerid
            FROM
                tac_playeringame
            GROUP BY 
                gameid 
            HAVING
                count(playerid) = 0
        ");

        if ($stmt)
        {
            $stmt->bind_result($id, $playerid);
            $stmt->execute();
            while ($stmt->fetch())
            {
                $ids[] = $id; 
            }
            $stmt->close();
        }
        
        $this->deleteGames($ids);
    }
	
	public function leaveSlot($userid, $gameid, $slotid = null){
		
        $userid = $this->DBEscape($userid);
        $gameid = $this->DBEscape($gameid);
        $slotid = $this->DBEscape($slotid);
		
		try{
			
            $sql = "DELETE FROM `B5CGM`.`tac_ship` WHERE tacgameid = $gameid AND playerid = $userid";
            if ($slotid)
                $sql .= " AND slot = $slotid";
            
            $this->update($sql);

            $sql = "UPDATE tac_playeringame SET playerid = null, lastphase = -3, lastturn = 0 WHERE gameid = $gameid AND playerid = $userid";
            if ($slotid)
                $sql .= " AND slot = $slotid";
            
            $this->update($sql);
			
			
		}catch(Exception $e) {
            throw $e;
        }
	}
	
		
	public function shouldBeInGameLobby($userid){
	
		try{
			$sql = "SELECT * FROM `B5CGM`.`tac_game` g join `B5CGM`.`tac_playeringame` p on g.id = p.gameid where p.playerid = $userid and g.status = 'LOBBY';";
			
			$result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
				return false;
				
			return $result[0]->id;
		}catch(Exception $e) {
            throw $e;
        }
		
	}
	
	public function takeSlot($userid, $gameid, $slotid){
	
        $userid = $this->DBEscape($userid);
        $gameid = $this->DBEscape($gameid);
        $slotid = $this->DBEscape($slotid);
		try{
			
            $slot = $this->getSlotById($slotid, $gameid);
            if (!$slot)
                return false;
            
			//already in slot on other team?
			$sql = "SELECT * FROM `B5CGM`.`tac_playeringame` WHERE gameid = $gameid AND teamid != ".$slot->team." AND playerid = $userid";
			if ($this->found($sql))
            {
                $this->leaveSlot($userid, $gameid);
            }
			
			$sql = "UPDATE tac_playeringame SET playerid = $userid WHERE gameid = $gameid and slot = $slotid";
			
			$this->update($sql);
			
			
		}catch(Exception $e) {
            throw $e;
        }
	}
    
    public function createSlots($gameid, $input){
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
                '0000-00-00 00:00:00',
                '0000-00-00 00:00:00',
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?,
                ?
            )

        ");

        if ($stmt)
        {
            foreach ($slots as $slot)
            {
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
	
	public function createGame($gamename, $background, $slots, $userid){
        $stmt = $this->connection->prepare("
            INSERT INTO 
                tac_game
            VALUES
            (
                null,
                ?,
                0,
                -2,
                -1,
                ?,
                0,
                'LOBBY',
                ?,
                ?,
                '0000-00-00 00:00:00'
            )
        ");

        if ($stmt)
        {
            $gamename = $this->DBEscape($gamename);
            $background = $this->DBEscape($background);
            $slotnum = count($slots);
            $stmt->bind_param(
                'ssii',
                $gamename,
                $background,
                $slotnum,   
                $userid
            );
            $stmt->execute();
            $stmt->close();
            $gameid = $this->getLastInstertID();
        }
        
        $this->createSlots($gameid, $slots);
        
        return $gameid;
	}
    
    public function submitCriticals($gameid, $criticals, $turn){
        
        try {
            
            //print(var_dump($criticals));
            foreach ($criticals as $critical){
                if ($critical->turn != $turn)
                    continue;
                
                $sql = "INSERT INTO `B5CGM`.`tac_critical` VALUES(null, $gameid, ".$critical->shipid.", ".$critical->systemid.",'".$critical->phpclass."', $turn, '".$critical->param."')";
    
                $this->update($sql);
            }
                
            
        }
        catch(Exception $e) {
            throw $e;
        }
    
    }
    
    public function updateFireOrders($fireOrders){
        
        $stmt = $this->connection->prepare(
            "UPDATE 
                tac_fireorder  
            SET 
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

        if ($stmt)
        {
            foreach ($fireOrders as $fire)
            {
                $stmt->bind_param(
                    'iissiiiiii',
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
        
    public function submitFireorders($gameid, $fireOrders, $turn, $phase){

        foreach ($fireOrders as $fire){
            if ($fire->turn != $turn)
                continue;

            if ($fire->type=="ballistic" && $phase != 1)
                continue;

            if ($fire->type!="ballistic" && $phase == 1)
                continue;

            $sql = "INSERT INTO `B5CGM`.`tac_fireorder` VALUES (null, '".$fire->type."', ".$fire->shooterid.", ".$fire->targetid.", ".$fire->weaponid.", ".$fire->calledid.", ".$fire->turn.", "
                    .$fire->firingMode.", ". $fire->needed.", ".$fire->rolled.", $gameid, '".$fire->notes."', ".$fire->shotshit.", ".$fire->shots.", '".$fire->pubnotes."', 0, '".$fire->x."', '".$fire->y."')";

            $this->update($sql);
        }
    }
    
    public function submitPower($gameid, $turn, $powers){
        
        try {
            
    
            foreach ($powers as $power){
                if ($power->turn != $turn)
                    continue;
                
                //$id, $shipid, $systemid, $type, $turn, $amount
                $sql = "INSERT INTO `B5CGM`.`tac_power` VALUES( null, ".$power->shipid.", ".$gameid.", ".$power->systemid.", ".$power->type.", ".$turn.
                    ", ".$power->amount.")";

                $this->update($sql);
            }
                
            
        }
        catch(Exception $e) {
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
                ?,?,?,?,?
            )
            ON DUPLICATE KEY UPDATE
                data = ?
            "
        );

        if ($stmt)
        {
            foreach ($datas as $data)
            {
                $json = $data->toJSON();
                $stmt->bind_param(
                    'iiiiss',
                    $data->systemid,
                    $data->subsystem,
                    TacGamedata::$currentGameID,
                    $data->shipid,
                    $json,
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
    
    public function submitDamages($gameid, $turn, $damages){
        
        try {
            
    
            foreach ($damages as $damage){
                                    
                $des = ($damage->destroyed) ? 1 : 0;
                
                //$id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields;
                $sql = "INSERT INTO `B5CGM`.`tac_damage` VALUES( null, ".$damage->shipid.", ".$gameid.", ".$damage->systemid.", ".$turn.", ".$damage->damage.
                    ", ".$damage->armour. ", ".$damage->shields.", ".$damage->fireorderid .", ".$des.", '".$damage->pubnotes."')";

                $this->update($sql);
            }
                
            
        }
        catch(Exception $e) {
            throw $e;
        }
    
    }
    
    public function submitIniative($gameid, $turn, $ships){
        
        try {
            
            foreach ($ships as $ship){
                $sql = "INSERT INTO `B5CGM`.`tac_iniative` VALUES($gameid, ".$ship->id.", $turn, ".$ship->iniative.")";
                
                $this->update($sql);
            }
        }
        catch(Exception $e) {
            throw $e;
        }
    
    }
    
    public function updatePlayerStatus($gameid, $userid, $phase, $turn, $slots = null){
        try {
            $sql = "UPDATE `B5CGM`.`tac_playeringame` SET `lastturn` = $turn, `lastphase` = $phase, `lastactivity` = NOW() WHERE"
            ." gameid = $gameid AND playerid = $userid";

            if ($slots){
                $slots = array_keys($slots);
                $slots = implode(',',$slots);
                $sql .= " AND slot IN ($slots)";
            }
            
            $this->update($sql);
        }
        catch(Exception $e) {
            throw $e;
        }
    
    }
    
    public function updateGamedata($gamedata){
        try {

            $sql = "UPDATE `B5CGM`.`tac_game` SET `turn` = ".$gamedata->turn.", `phase` = ".$gamedata->phase.", `activeship` = ".$gamedata->activeship.", `status` = '".$gamedata->status."'  WHERE id = ".$gamedata->id;
            $this->update($sql);
        }
        catch(Exception $e) {
            throw $e;
        }
        
    }
    
    public function submitEW($gameid, $shipid, $EW, $turn){
        try {
                
            foreach ($EW as $entry){
                
                if($entry->turn != $turn)
                    continue;
                
                $sql ="INSERT INTO `B5CGM`.`tac_ew` VALUES (null, $gameid, ".$entry->shipid.", $turn, '".$entry->type."', ".$entry->amount.", ". $entry->targetid.")";
    
                $this->insert($sql);
            }
    
        }
        catch(Exception $e) {

            throw $e;
        }
        
    }
    
    public function insertShips($gameid, $ships)
    {
        foreach ($ships as $ship)
        {
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
                    null,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?
                )"
            );
            
			if ($stmt)
            {
                foreach ($moves as $move)
                {
                    $preturn = ($move->preturn) ? 1 : 0;
                    $reqThrust = $move->getReqThrustJSON();
                    $assThrust = $move->getAssThrustJSON();
                    
                    $stmt->bind_param(
                        'iisiiiiiiiissii',
                        $shipid,
                        $gameid,
                        $move->type,
                        $move->x,
                        $move->y,
                        $move->xOffset,
                        $move->yOffset,
                        $move->speed,
                        $move->heading,
                        $move->facing,
                        $preturn,
                        $reqThrust,
                        $assThrust,
                        $move->turn,
                        $move->value
                    );
                    $stmt->execute();
                }
                $stmt->close();
            }
        }
        catch(Exception $e) {
            throw $e;
        }
    }
	
    public function submitMovement($gameid, $shipid, $turn, $movements, $acceptPreturn = false){
        try {
            
            foreach ($movements as $movement){
                
                if($movement->type == "start" || $movement->turn != $turn)
                    continue;
                
                $preturn = ($movement->preturn) ? 1 : 0;
        
                if ($acceptPreturn == false && $preturn)
                    continue;
                
                $sql = "Insert into `B5CGM`.`tac_shipmovement` values (null, $shipid, $gameid, '".$movement->type."', ".$movement->x.", ".$movement->y.", ".$movement->xOffset.", ".$movement->yOffset.", ".$movement->speed.", ".$movement->heading.", ".$movement->facing.", $preturn, '".$movement->getReqThrustJSON()."', '".$movement->getAssThrustJSON()."', $turn, '".$movement->value."')";
                
                //throw new exception("sql: ".$movement->preturn . var_dump($movement));
                $this->insert($sql);
            }
    
        }
        catch(Exception $e) {

            throw $e;
        }
    }

    
	public function getTacGames($playerid){
		$games = $this->getTacGame(0, $playerid, false);
		if ($games == null)
			return array();
        
		foreach ($games as $game){
			$game->slots = $this->getSlotsInGame($game->id);
		}
		
		return $games;
	}
	
    public function getTacGamedata($playerid, $gameid){
		
		if ($gameid <=0)
			return null;
		
        $gamedata = $this->getTacGame($gameid, $playerid);
		if ($gamedata == null)
			return null;

        $gamedata->slots = $this->getSlotsInGame($gameid);
        $this->getTacShips($gamedata);
		$gamedata->onConstructed();
        
        
        return $gamedata;
    }
    
    public function getTacGame($gameid, $playerid, $finished = true){
	
		if ($gameid >0){
			$sql = "SELECT * FROM `B5CGM`.`tac_game` where id = $gameid";
			if (!$finished){
				$sql .= " AND `status` <> 'FINISHED'";
			}
		}else{
			$sql = "SELECT * FROM `B5CGM`.`tac_game`";
			if (!$finished){
				$sql .= " WHERE `status` <> 'FINISHED'";
			}
		}
		
		
		
		$games = array();
        
         try {
            $result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
                return null;
                
            foreach ($result as $value) {
                                               
              
                
                $game = new TacGamedata($value->id, $value->turn, $value->phase, $value->activeship, $playerid, $value->name, $value->status, $value->points, $value->background, $value->creator);
				$games[] = $game;
            }
            
            //$this->close();
            
            }
            catch(Exception $e) {
                throw $e;
            }
			
			if ($gameid>0){
				return $games[0];
			}else{
				return $games;
			}
           
    }
    
    public function getSlotsInGame($gameid){
        
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

        if ($stmt)
        {
            $stmt->bind_param('i', $gameid);
            $stmt->bind_result($playerid, $slot, $teamid, $lastturn, $lastphase, $name, $points, $depx, $depy, $deptype, $depwidth, $depheight, $depavailable, $username);
            $stmt->execute();
            while ($stmt->fetch())
            {
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

        if ($stmt)
        {
            $stmt->bind_param('ii', $gameid, $slotid);
            $stmt->bind_result($playerid, $slot, $teamid, $lastturn, $lastphase, $name, $points, $depx, $depy, $deptype, $depwidth, $depheight, $depavailable, $username);
            $stmt->execute();
            while ($stmt->fetch())
            {
                $slot = new PlayerSlot($playerid, $slot, $teamid, $lastturn, $lastphase, $name, $points, $depx, $depy, $deptype, $depwidth, $depheight, $depavailable, $username);
            }
            $stmt->close();
        }
        return $slot;
    }
    
    public function getTacShips($gamedata)
    {
        
        $starttime = time();  
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

        if ($stmt)
        {
            $stmt->bind_param('i', $gamedata->id);
            $stmt->bind_result($id, $playerid, $name, $phpclass, $slot);
            $stmt->execute();
            while ($stmt->fetch())
            {
                $ship = new $phpclass($id, $playerid, $name, $slot);
                $ship->team = $gamedata->slots[$slot]->team;
                $ships[] = $ship;
            }
            $stmt->close();
        }
        
        $gamedata->setShips($ships);
        
        $this->getIniativeForShips($gamedata);
        $this->getMovesForShips($gamedata);
        $this->getEWForShips($gamedata);
        $this->getPowerForShips($gamedata);
        $this->getCriticalsForShips($gamedata);
        $this->getDamageForShips($gamedata);
        $this->getFireOrdersForShips($gamedata);
        $this->getSystemDataForShips($gamedata);
        
        $endtime = time();  
        Debug::log("GETTING SHIPS - GAME: $gamedata->id Fetching gamedata took " . ($endtime - $starttime) . " seconds.");
        
        
    }
    
    private function getIniativeForShips($gamedata){
        
        
        $stmt = $this->connection->prepare(
            "SELECT
                iniative, shipid
            FROM
                tac_iniative 
            WHERE
                gameid = ?
            AND
                turn = ?
            "
        );

        if ($stmt)
        {
            $stmt->bind_param('ii', $gamedata->id, $gamedata->turn);
            $stmt->bind_result($iniative, $shipid);
            $stmt->execute();
            while ($stmt->fetch())
            {
                $gamedata->getShipById($shipid)->iniative = $iniative;
            }
            $stmt->close();
        }
        
        
    }
    
    private function getMovesForShips($gamedata){
        
        $stmt = $this->connection->prepare(
            "SELECT 
                id, shipid, type, x, y, xOffset, yOffset, speed, heading, facing, preturn, turn, value, requiredthrust, assignedthrust
            FROM 
                tac_shipmovement
            WHERE
                gameid = ?
            AND
                ( turn >= ? OR turn = 1 ) 
            ORDER BY
                id ASC
            "
        );

        if ($stmt)
        {
            $fetchturn = $gamedata->turn-1;
            $stmt->bind_param('ii', $gamedata->id, $fetchturn);
            $stmt->bind_result($id, $shipid, $type, $x, $y, $xOffset, $yOffset, $speed, $heading, $facing, $preturn, $turn, $value, $requiredthrust, $assignedthrust);
            $stmt->execute();
            while ($stmt->fetch())
            {
                $move = new MovementOrder($id, $type, $x, $y, $xOffset, $yOffset, $speed, $heading, $facing, $preturn, $turn, $value);
                $move->setReqThrustJSON($requiredthrust);
                $move->setAssThrustJSON($assignedthrust);

                $gamedata->getShipById($shipid)->setMovement( $move );
            }
                
            $stmt->close();
        }
        
        
    }
    
    private function getEWForShips($gamedata){
        
        $stmt = $this->connection->prepare(
            "SELECT 
                id, shipid, turn, type, amount, targetid
            FROM 
                tac_ew 
            WHERE 
                gameid = ?
            AND
                turn >= ? 
            ORDER BY
                id ASC
            "
        );

        if ($stmt)
        {
            $fetchturn = $gamedata->turn-1;
            $stmt->bind_param('ii', $gamedata->id, $fetchturn);
            $stmt->bind_result($id, $shipid, $turn, $type, $amount, $targetid);
            $stmt->execute();
            while ($stmt->fetch())
            {
                $gamedata->getShipById($shipid)->setEW(
                    new EWentry($id, $shipid, $turn, $type, $amount, $targetid)
                );
            }

        }
        
        
    }
    
    private function getDamageForShips($gamedata)
    {
        $damageStmt = $this->connection->prepare(
            "SELECT 
                id, shipid, gameid, turn, systemid, damage, armour, shields, fireorderid, destroyed, pubnotes 
            FROM
                tac_damage
            WHERE 
                gameid = ?
            "
        );
        
        if ($damageStmt)
        {
            $damageStmt->bind_param('i', $gamedata->id);
            $damageStmt->bind_result($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $pubnotes );
            $damageStmt->execute();
            while ($damageStmt->fetch())
            {
                $gamedata->getShipById($shipid)->getSystemById($systemid)->setDamage(
                    new DamageEntry($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $pubnotes )
                );
            }
            $damageStmt->close();
        }
        
        
    }
    
    private function getCriticalsForShips($gamedata)
    {
        $criticalStmt = $this->connection->prepare(
            "SELECT 
                id, shipid, systemid, type, turn, param 
            FROM 
                tac_critical
            WHERE 
                gameid = ?
            "
        );
        
        if ($criticalStmt)
        {
            $criticalStmt->bind_param('i', $gamedata->id);
            $criticalStmt->bind_result($id, $shipid, $systemid, $type, $turn, $param);
            $criticalStmt->execute();
            while ($criticalStmt->fetch())
            {
                $gamedata->getShipById($shipid)->getSystemById($systemid)->setCritical(
                    new $type($id, $shipid, $systemid, $type, $turn, $param),
                    $gamedata->turn
                );
            }
            $criticalStmt->close();
        }
        
        
    }
    
    private function getPowerForShips($gamedata)
    {
        $powerStmt = $this->connection->prepare(
            "SELECT
                id, shipid, systemid, type, turn, amount 
            FROM
                tac_power
            WHERE 
                gameid = ?
            AND 
                turn >= ?
            "
        );
        
        if ($powerStmt)
        {
            $fetchturn = $gamedata->turn-1;
            $powerStmt->bind_param('ii', $gamedata->id, $fetchturn);
            $powerStmt->bind_result($id, $shipid, $systemid, $type, $turn, $amount );
            $powerStmt->execute();
            while ($powerStmt->fetch())
            {
                $gamedata->getShipById($shipid)->getSystemById($systemid)->setPower(
                    new PowerManagementEntry($id, $shipid, $systemid, $type, $turn, $amount)
                );
            }
            $powerStmt->close();
        }
        
        
    }
    
    public function getSystemDataForShips($gamedata)
    {
        $loading = array();
        $stmt = $this->connection->prepare(
            "SELECT 
                data, shipid, systemid, subsystem
            FROM 
                tac_systemdata
            WHERE 
                gameid = ?"
        );

        if ($stmt)
        {
            $stmt->bind_param('i', $gamedata->id);
            $stmt->execute();
            $stmt->bind_result(
                $data,
                $shipid,
                $systemid,
                $subsystem
            );

            while( $stmt->fetch())
            {
                $gamedata->getShipById($shipid)->getSystemById($systemid)->setSystemData(
                    $data, $subsystem
                );
            }
            $stmt->close();
        }
    }
    
    public function getFireOrdersForShips($gamedata)
    {
        $stmt = $this->connection->prepare(
            "SELECT 
                *
            FROM 
                tac_fireorder
            WHERE 
                gameid = ? 
            AND 
                turn = ?"
        );

        if ($stmt)
        {
            $stmt->bind_param('ii', $gamedata->id, $gamedata->turn);
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
                $y
            );

            while( $stmt->fetch())
            {
                $entry = new FireOrder(
                    $id, $type, $shooterid, $targetid,
                    $weaponid, $calledid, $turn, $firingMode, $needed, 
                    $rolled, $shots, $shotshit, $intercepted, $x, $y
                );

                $entry->notes = $notes;
                $entry->pubnotes = $pubnotes;
                
                $gamedata->getShipById($shooterid)->getSystemById($weaponid)->setFireOrder( $entry );
            }
            $stmt->close();
        }
    }
    
    public function isNewGamedata($gameid, $turn, $phase, $activeship){
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
                
                if ($dbphase != $phase || $dbturn != $turn || $dbactiveship != $activeship)
                    return true;
                
			}
                     
            return false;
           
        }
        catch(Exception $e) {
            throw $e;
        }
        
    }
  
    public function authenticatePlayer($username, $password){
	
        $id = false;
        try {
			if ($stmt = $this->connection->prepare(
                "SELECT id FROM player where username = ? and password = password(?)")) {
				
				$stmt->bind_param('ss', $username, $password);
				$stmt->execute();
				$stmt->bind_result($id);
				$stmt->fetch();
				
				/* close statement */
				$stmt->close();
			}
                     
            if (!$id)
				return false;
           
        }
        catch(Exception $e) {
            throw $e;
        }
        
        return $id;
    }  
    
    public function releaseGameSubmitLock($gameid)
    {
        try {
			if ($stmt = $this->connection->prepare(
                "UPDATE 
                    tac_game 
                SET
                    submitLock = '0000-00-00 00:00:00'
                WHERE 
                    id = ?
                "
            ))
            {
				
				$stmt->bind_param('i', $gameid);
				$stmt->execute();
				
				$stmt->close();
			}
        }
        catch(Exception $e) {
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
                    submitLock = '0000-00-00 00:00:00'
                WHERE 
                    gameid = ?
                AND
                    playerid = ?
                "
            ))
            {
				$stmt->bind_param('ii', $gameid, $playerid);
				$stmt->execute();
				$stmt->close();
			}
        }
        catch(Exception $e) {
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
                    submitLock = '0000-00-00 00:00:00'
                )"
            ))
            {
				
				$stmt->bind_param('i', $gameid);
				$stmt->execute();
				
                if ($stmt->affected_rows == 1)
                    $result = true;
				
				/* close statement */
				$stmt->close();
			}
        }
        catch(Exception $e) {
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
                    submitLock = '0000-00-00 00:00:00'
                )"
            ))
            {
				
				$stmt->bind_param('ii', $gameid, $playerid);
				$stmt->execute();
				
                if ($stmt->affected_rows > 0)
                    $result = true;
				
				/* close statement */
				$stmt->close();
			}
        }
        catch(Exception $e) {
            throw $e;
        }
        
        return $result;
    }
    
    public function checkIfPhaseReady($gameid)
    {
        try {
            $stmt = $this->connection->prepare(
                "SELECT 
                    g.id, g.slots, p.playerid
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
            
			if ($stmt)
            {
				$stmt->bind_param('i', $gameid);
				$stmt->execute();
                $stmt->bind_result($id, $slots, $playerid);
				$stmt->fetch();
				$stmt->close();
                
                if ($id)
                {
                    return true;
                }
				
				
			}
        }
        catch(Exception $e) {
            throw $e;
        }
        
        return false;
        
    }
    
    public function getGamesToBeDeleted( )
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
                DATE_ADD(p.lastactivity, INTERVAL 1 MONTH) < NOW()
            OR
                (DATE_ADD(p.lastactivity, INTERVAL 1 DAY) < NOW() 
                AND
                g.status = 'LOBBY')

        ");

        if ($stmt)
        {
            $stmt->bind_result($id);
            $stmt->execute();
            while ($stmt->fetch())
            {
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
			
        }
        catch(Exception $e) {
            throw $e;
        }
    }
    
    private function executeGameDeleteStatement($stmt, $ids)
    {
        if ($stmt)
        {
            foreach ($ids as $id)
            {
                $stmt->bind_param('i', $id);
                $stmt->execute();
            }
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
        
        if ($stmt)
        {
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
            ORDER BY id ASC
        ");
        
        if ($stmt)
        {
            $stmt->bind_param('ii', $gameid, $lastid);
            $stmt->bind_result($id, $userid, $username, $gameid, $message, $time);
            $stmt->execute();
            while ($stmt->fetch())
            {
                $messages[] = 
                    new ChatMessage($id, $userid, $username, $gameid, $message, $time);
            }
            $stmt->close();
        }
        
        return $messages;
    }
    
    public function deleteOldChatMessages(){
        $stmt = $this->connection->prepare("
            DELETE FROM
                chat
            WHERE
                DATE_ADD(time, INTERVAL 1 DAY) < NOW()    
        ");
        
        if ($stmt)
        {
            $stmt->execute();
            $stmt->close();
        }
        
    }
  
    //UTILS
    
    public function chekcIfTableExists($name, $close = true){
        $sql = "show tables like '$name'";
        
        try {
            $result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
                return false;
                
                   
        }
        catch(Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    public function createDatabase($sql){
        $a = explode(";", $sql);
        try{
            foreach($a as $line){
                $line = trim($line);
                if (empty($line))
                    continue;
                        
                $this->update(trim($line));
            }
       
        }catch(Exception $e) {
            throw $e;
        }
    }
}
?>
