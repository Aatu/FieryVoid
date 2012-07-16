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
            
        if (!$answer = mysqli_query($this->connection, $sql)){
            $this->endTransaction(true);
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
			
			$sql = "INSERT INTO `B5CGM`.`tac_ship` VALUES(null, $userid, $gameid, '".$this->DBEscape($ship->name)."', '".$ship->phpclass."', 0, 0, 0, 0, 0)";
			$id = $this->insert($sql);
			
			$sql = "INSERT INTO `B5CGM`.`tac_iniative` VALUES($gameid, $id, 0, 0)";
            $this->insert($sql);			
			
		}catch(Exception $e) {
			$this->endTransaction(true);
            throw $e;
        }
	}
	
	public function leaveSlot($userid){
		
		
		try{
			
			$sql = "SELECT * FROM `B5CGM`.`tac_game` g join `B5CGM`.`tac_playeringame` p on g.id = p.gameid where p.playerid = $userid and g.status = 'LOBBY';";
			
			$result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
				return false;
				
			foreach ($result as $value){
			
				if ($value->creator == $userid){
					$sql = "DELETE FROM `B5CGM`.`tac_playeringame` WHERE gameid = ".$value->id;
					$this->update($sql);
					
					$sql = "DELETE FROM `B5CGM`.`tac_ship` WHERE tacgameid = ".$value->id;
					$this->update($sql);
					
					$sql = "DELETE FROM `B5CGM`.`tac_iniative` WHERE gameid = ".$value->id;
					$this->update($sql);
					
					$sql = "DELETE FROM `B5CGM`.`tac_game` WHERE id = ".$value->id;
					$this->update($sql);
					
				}else{
					$sql = "DELETE FROM `B5CGM`.`tac_ship` WHERE tacgameid = ".$value->id." AND playerid = $userid";
					$this->update($sql);
					
					$sql = "DELETE FROM `B5CGM`.`tac_playeringame` WHERE gameid = ".$value->id . " AND playerid = $userid";
					$this->update($sql);
				}
			
				
				

				
			}
			
				
			
		}catch(Exception $e) {
			$this->endTransaction(true);
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
	
	public function takeSlot($userid, $gameid, $slot){
	
		try{
			
			//already in slot?
			$sql = "SELECT * FROM `B5CGM`.`tac_playeringame` WHERE gameid = $gameid AND slot = $slot";
			if ($this->found($sql))
				return false;
			

			//already in slot in another game, that has status "LOBBY"?
			$sql = "SELECT * FROM `B5CGM`.`tac_game` g join `B5CGM`.`tac_playeringame` p on g.id = p.gameid where p.playerid = $userid and g.status = 'LOBBY';";
			
			$result = $this->query($sql);
            				
			foreach ($result as $value){
						
				$sql = "DELETE FROM `B5CGM`.`tac_playeringame` WHERE gameid = ".$value->id . " AND playerid = $userid";
						
				$this->update($sql);
							
			}
					
			
			$sql = "INSERT INTO `B5CGM`.`tac_playeringame` VALUES ( $gameid, $slot, $userid, $slot, 0, -3, now(), '0000-00-00 00:00:00')";
			
			$this->insert($sql);
			
			
		}catch(Exception $e) {
            throw $e;
        }
	}
	
	public function createGame($name, $background, $maxplayers, $points, $userid){
	
		try{
			$sql = "INSERT INTO `B5CGM`.`tac_game` VALUES (	null,'".$this->DBEscape($name)."',0,-2,-1,'$background',$points, 'LOBBY', 2, $userid, 0)";
			$id = $this->insert($sql);
			
			return $id;
		}catch(Exception $e) {
            throw $e;
        }
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
    
        Debug::log("Upadting FireOrders: " . var_export($fireOrders, true));
        
        foreach ($fireOrders as $fire){
            try {
                $sql = "UPDATE `B5CGM`.`tac_fireorder` SET `needed` = ".$fire->needed.", `rolled` = ".$fire->rolled.", `notes` = '".$fire->notes.
                "', `pubnotes` = '".$fire->pubnotes."', `shots` = ".$fire->shots.", `shotshit` = ".$fire->shotshit.", `intercepted` = "
                .$fire->intercepted.", `x` = '".$fire->x."', `y` = '".$fire->y."' WHERE id = ".$fire->id;

                $this->update($sql);
            }
            catch(Exception $e) {
                throw $e;
            }
            
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
    
    public function insertWeaponLoading($input)
    {
        $loadings = array();
        if (is_array($input))
            $loadings = $input;
        else 
            $loadings[] = $input;
        
        try {
            $stmt = $this->connection->prepare(
                "INSERT INTO  
                    tac_loading
                VALUES 
                ( 
                    ?,?,?,?,?,?,?,?
                )"
            );
            
			if ($stmt)
            {
                foreach ($loadings as $loading)
                {
                    $stmt->bind_param(
                        'iiiiiiii',
                        $loading->systemid,
                        $loading->subsystem,
                        $loading->gameid,
                        $loading->shipid,
                        $loading->loading,
                        $loading->extrashots,
                        $loading->loadedammo,
                        $loading->overloading
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
    
    public function updateWeaponLoading($input)
    {
        Debug::log("updateWeaponLoading: " . var_export($input, true));
        $loadings = array();
        if (is_array($input))
            $loadings = $input;
        else 
            $loadings[] = $input;
        
        try {
            $stmt = $this->connection->prepare(
                "UPDATE 
                    tac_loading
                SET 
                    loading = ?, 
                    extrashots = ?,
                    loadedammo = ?,
                    overloading = ?
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
                foreach ($loadings as $loading)
                {
                    $stmt->bind_param(
                        'iiiiiiii', 
                        $loading->loading,
                        $loading->extrashots,
                        $loading->loadedammo,
                        $loading->overloading,
                        $loading->gameid,
                        $loading->systemid,
                        $loading->shipid,
                        $loading->subsystem
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
    
    
    public function getWeaponLoading($shipid, $gameid, $systemid)
    {
        $loading = array();
        try {
            $stmt = $this->connection->prepare(
                "SELECT 
                    systemid, subsystem, gameid, shipid, loading, extrashots, loadedammo, overloading
                FROM 
                    tac_loading
                WHERE 
                    gameid = ? 
                AND 
                    systemid = ?
                AND 
                    shipid = ?"
            
                
            );
            
			if ($stmt)
            {
				$stmt->bind_param('iii', $gameid, $systemid, $shipid);
				$stmt->execute();
                $stmt->bind_result(
                    $systemid, $subsystem, $gameid, $shipid, $turnsloaded, $extrashots, $loadedammo, $overloading
                );
                
                while( $stmt->fetch())
                {
                    $loading[] = new WeaponLoading($systemid, $subsystem, $gameid, $shipid, $turnsloaded, $extrashots, $loadedammo, $overloading);
                    
                }
				$stmt->close();
			}
        }
        catch(Exception $e) {
            throw $e;
        }
        
        return $loading;
    }
    
    public function getFireOrders($shipid, $gameid, $systemid, $turn)
    {
        $orders = array();
        try {
            $stmt = $this->connection->prepare(
                "SELECT 
                    *
                FROM 
                    tac_fireorder
                WHERE 
                    gameid = ? 
                AND 
                    shooterid = ?
                AND 
                    weaponid = ?
                AND 
                    turn = ?"
            );
            
			if ($stmt)
            {
				$stmt->bind_param('iiii', $gameid, $shipid, $systemid, $turn);
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
                    $orders[] = $entry;
                }
				$stmt->close();
			}
        }
        catch(Exception $e) {
            throw $e;
        }
        
        return $orders;
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
    
    public function updatePlayerStatus($gameid, $userid, $phase, $turn){
        try {
            $sql = "UPDATE `B5CGM`.`tac_playeringame` SET `lastturn` = $turn, `lastphase` = $phase, `lastactivity` = NOW() WHERE"
            ." gameid = $gameid AND playerid = $userid";

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
        foreach ($ships as $ship)
        {
            foreach ($ship->systems as $system)
            {
                if ($system instanceof Weapon)
                {
                    $this->insertWeaponLoading($system->getStartLoading($gameid, $ship));
                }
                
                if ($system instanceof Fighter)
                {
                    foreach ($system->systems as $fighterSystem)
                    {
                        if ($fighterSystem instanceof Weapon)
                        {
                            $loading = new WeaponLoading($fighterSystem->id, $gameid, $ship->id, $fighterSystem->getNormalLoad(), 0, 0, 1);
                            $this->insertWeaponLoading($fighterSystem->getStartLoading($gameid, $ship)); 
                        }
                     
                    }
                }
            }

        }
    }
    
    /*
	public function deploy($gameid, $shipid, $movement)
    {
		try {
           	
			$preturn = ($movement->preturn) ? 1 : 0;
			
			$sql = "Insert into `B5CGM`.`tac_shipmovement` values (null, $shipid, $gameid, '".$movement->type."', ".$movement->x.", ".$movement->y.
                ", ".$movement->xOffset.", ".$movement->yOffset.", ".$movement->speed.", ".$movement->heading
                .", ".$movement->facing.", $preturn, '".$movement->getReqThrustJSON()."', '".$movement->getAssThrustJSON()."', 0, 0)";
			
			//throw new exception("sql: ".$movement->preturn . var_dump($movement));
			$this->insert($sql);
		
    
        }
        catch(Exception $e) {

            throw $e;
        }
	}*/
    
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
                        $move->getReqThrustJSON(),
                        $move->getAssThrustJSON(),
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
			$game->players = $this->getPlayersInGame($playerid, $game->id);
		}
		
		return $games;
	}
	
    public function getTacGamedata($playerid, $gameid){
		
		if ($gameid <=0)
			return null;
		
        $gamedata = $this->getTacGame($gameid, $playerid);
		if ($gamedata == null)
			return null;

        $gamedata->players = $this->getPlayersInGame($playerid, $gameid);
        $gamedata->setShips( $this->getTacShips($gameid, $gamedata->players, $gamedata->turn, $gamedata->phase) );
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
    
    public function getPlayersInGame($playerid, $gameid){
        $sql = "SELECT * FROM `B5CGM`.`tac_playeringame` pg join `B5CGM`.`player` p on p.id = pg.playerid where pg.gameid = $gameid";
        
        $players = array();
         try {
            $result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
                return null;
                
                foreach ($result as $value) {
                    $players[$value->playerid] = new TacticalPlayer($value->playerid, $value->slot, $value->teamid, $value->lastturn, $value->lastphase, $value->username);
                    
                }
            
            }
            catch(Exception $e) {
                throw $e;
            }
        
        return $players;
    }
    
    public function getTacShips($gameid, $players, $turn, $phase)
    {
        
        $starttime = time();
        $ships = array();
        try {
            $stmt = $this->connection->prepare(
                "SELECT
                    id, playerid, name, phpclass 
                FROM
                    tac_ship 
                WHERE
                    tacgameid = ?
                "
            );
            
			if ($stmt)
            {
                $stmt->bind_param('i', $gameid);
                $stmt->bind_result($id, $playerid, $name, $phpclass);
				$stmt->execute();
				while ($stmt->fetch())
                {
                   $ship = new $phpclass($id, $playerid, $name, null);
                   $ship->team = $players[$playerid]->team;
                   $ships[] = $ship;
                }
				$stmt->close();
			}
        }
        catch(Exception $e) {
            throw $e;
        }
        
        $this->getIniativeForShips($gameid, $turn, $ships);
        $this->getMovesForShips($gameid, $turn, $ships);
        $this->getEWForShips($gameid, $turn, $ships);
        $this->getSystemDataForShips($gameid, $turn, $ships);
        
        $endtime = time();
        Debug::log("GETTING SHIPS - GAME: $gameid Fetching gamedata took " . ($endtime - $starttime) . " seconds.");
        return $ships;
        
        
    }
    
    private function getIniativeForShips($gameid, $turn, $ships){
        
        
        $stmt = $this->connection->prepare(
            "SELECT
                iniative
            FROM
                tac_iniative 
            WHERE
                gameid = ?
            AND
                shipid = ?
            AND 
                turn = ?
            "
        );

        if ($stmt)
        {
            foreach ($ships as $ship)
            {
                $stmt->bind_param('iii', $gameid, $ship->id, $turn);
                $stmt->bind_result($iniative);
                $stmt->execute();
                while ($stmt->fetch())
                {
                    $ship->iniative = $iniative;
                }
            }
            $stmt->close();
        }
        
        
    }
    
    private function getMovesForShips($gameid, $gameturn, $ships){
        
        $gameturn = $gameturn - 1;
        $stmt = $this->connection->prepare(
            "SELECT 
                id, type, x, y, xOffset, yOffset, speed, heading, facing, preturn, turn, value, requiredthrust, assignedthrust
            FROM 
                tac_shipmovement
            WHERE
                gameid = ?
            AND
                shipid = ?
            AND
                ( turn >= ? OR turn = 1 ) 
            ORDER BY
                id ASC
            "
        );

        if ($stmt)
        {
            foreach ($ships as $ship)
            {
                $moves = array();
                
                $stmt->bind_param('iii', $gameid, $ship->id, $gameturn);
                $stmt->bind_result($id, $type, $x, $y, $xOffset, $yOffset, $speed, $heading, $facing, $preturn, $turn, $value, $requiredthrust, $assignedthrust);
                $stmt->execute();
                while ($stmt->fetch())
                {
                    $move = new MovementOrder($id, $type, $x, $y, $xOffset, $yOffset, $speed, $heading, $facing, $preturn, $turn, $value);
                    $move->setReqThrustJSON($requiredthrust);
                    $move->setAssThrustJSON($assignedthrust);
            
                    $moves[] = $move;
                }
                
                $ship->setMovement($moves);
            }
            $stmt->close();
        }
        
        
    }
    
    private function getEWForShips($gameid, $gameturn, $ships){
        
        $gameturn = $gameturn - 1;
        $stmt = $this->connection->prepare(
            "SELECT 
                id, shipid, turn, type, amount, targetid
            FROM 
                tac_ew 
            WHERE 
                gameid = ?
            AND
                shipid = ?
            AND
                turn >= ? 
            ORDER BY
                id ASC
            "
        );

        if ($stmt)
        {
            foreach ($ships as $ship)
            {
                $ews = array();
                
                $stmt->bind_param('iii', $gameid, $ship->id, $gameturn);
                $stmt->bind_result($id, $shipid, $turn, $type, $amount, $targetid);
                $stmt->execute();
                while ($stmt->fetch())
                {
                    $ews[] = new EWentry($id, $shipid, $turn, $type, $amount, $targetid);
                }
                
                $ship->EW = $ews;
            }
            $stmt->close();
        }
        
        
    }
    
    private function getSystemDataForShips($gameid, $gameturn, $ships){
        $fetchturn = $gameturn - 1;
        $damageStmt = $this->connection->prepare(
            "SELECT 
                id, shipid, gameid, turn, systemid, damage, armour, shields, fireorderid, destroyed, pubnotes 
            FROM
                tac_damage
            WHERE 
                gameid = ?
            AND 
                shipid = ?
            AND 
                systemid = ?
            "
        );
        
        $powerStmt = $this->connection->prepare(
            "SELECT
                id, shipid, systemid, type, turn, amount 
            FROM
                tac_power
            WHERE 
                gameid = ?
            AND 
                shipid = ?
            AND 
                systemid = ?
            AND 
                turn >= ?
            "
        );
        
        $criticalStmt = $this->connection->prepare(
            "SELECT 
                id, shipid, systemid, type, turn, param 
            FROM 
                tac_critical
            WHERE 
                gameid = ?
            AND 
                shipid = ?
            AND 
                systemid = ?
            "
        );
        

        if ($damageStmt && $powerStmt && $criticalStmt)
        {
            foreach ($ships as $ship)
            {
                foreach ($ship->systems as $system)
                {
                    $damages = array();

                    $damageStmt->bind_param('iii', $gameid, $ship->id, $system->id);
                    $damageStmt->bind_result($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $pubnotes );
                    $damageStmt->execute();
                    while ($damageStmt->fetch())
                    {
                        $damages[] = new DamageEntry($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $pubnotes );
                    }

                    $system->setDamage($damages);
                    
                    $power = array();

                    $powerStmt->bind_param('iiii', $gameid, $ship->id, $system->id, $fetchturn);
                    $powerStmt->bind_result($id, $shipid, $systemid, $type, $turn, $amount );
                    $powerStmt->execute();
                    while ($powerStmt->fetch())
                    {
                        $power[] = new PowerManagementEntry($id, $shipid, $systemid, $type, $turn, $amount);
                    }

                    $system->setPower($power);
                    
                    $criticals = array();

                    $criticalStmt->bind_param('iii', $gameid, $ship->id, $system->id);
                    $criticalStmt->bind_result($id, $shipid, $systemid, $type, $turn, $param);
                    $criticalStmt->execute();
                    while ($criticalStmt->fetch())
                    {
                        $criticals[] = new $type($id, $shipid, $systemid, $type, $turn, $param);
                    }

                    $system->setCriticals($criticals, $gameturn);
                    
                    
                    if ($system instanceof Weapon){
                        $system->setLoading($this->getWeaponLoading($ship->id, $gameid, $system->id));
                        $system->setFireOrders($this->getFireOrders($ship->id, $gameid, $system->id, $gameturn));
                    }
                    else if ($system instanceof Fighter)
                    {
                        foreach ($system->systems as $fighterSystem)
                        {
                            if ($fighterSystem instanceof Weapon)
                            {
                                $fighterSystem->setLoading($this->getWeaponLoading($ship->id, $gameid, $fighterSystem->id));
                                $fighterSystem->setFireOrders($this->getFireOrders($ship->id, $gameid, $fighterSystem->id, $gameturn));
                            }
                                
                        }
                    }
                    
                }
            }
            $powerStmt->close();
            $criticalStmt->close();
            $damageStmt->close();
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
				
                if ($stmt->affected_rows === 1)
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
                    count(p.playerid) = g.slots;"
            );
            
			if ($stmt)
            {
				$stmt->bind_param('i', $gameid);
				$stmt->execute();
                $stmt->bind_result($id, $slots);
				$stmt->fetch();
				$stmt->close();
                
                if ($id)
                    return true;
				
				
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
        try {
            $stmt = $this->connection->prepare(
                "SELECT 
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
                "
            );
            
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
        }
        catch(Exception $e) {
            throw $e;
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
                    tac_loading
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
