<?php 
class DBManager {

    private $connection;
    
    function __construct($host, $port = 3306, $database, $username, $password) {
        if (!$this->connection = mysql_connect($host, $username, $password, $database, $port))
            throw new CustomException(300,"DBManager:Construct, connection failed: ".mysql_connect_error(), mysql_connect_errno(), null);
            
        if (!mysql_select_db($database, $this->connection))
            throw new CustomException(300,"DBManager:Construct, connection failed: ".mysql_error($this->connection), mysql_errno(), null);
            
        mysql_set_charset('utf8', $this->connection);
    }
    
    private function DBEscape($string) {
            
        return mysql_real_escape_string((String)$string, $this->connection);
    }
    
    private function query($sql) {

    
        if (!$this->connection)
            throw new Exception("DBManager:query, connection failed");
            
        if (!$answer = mysql_query($sql, $this->connection)){
            throw new Exception("DBManager:query, SQL error: ".mysql_error($this->connection)."\n sql: $sql error:", mysql_errno($this->connection));
        }
            
        $result = array();
				
		while ($row = mysql_fetch_object($answer)) {
			$result[] = $row;
		}
		
        return $result;
    }
    
    private function insert($sql) {

    
        if (!$this->connection)
            throw new exception("DBManager:insert, connection failed");
            
        if (!$answer = mysql_query($sql, $this->connection))
            throw new exception("DBManager:insert, SQL error: ".mysql_error($this->connection)."\n sql: $sql". mysql_errno($this->connection));
            
        $sql = "select LAST_INSERT_ID() as id";
        
        if (!$answer = mysql_query($sql, $this->connection))
           throw new exception("DBManager:insert, SQL (getting the id) error: ".mysql_error($this->connection)."\n sql: $sql", mysql_errno($this->connection));

            
        while ($row = mysql_fetch_object($answer)) {
            return $row->id;
        }
        
        return null;

        
    }
    
    public function update($sql) {

    
        if (!$this->connection)
            throw new exception("DBManager:update, connection failed");
            
        if (!$answer = mysql_query($sql, $this->connection)){
            $this->endTransaction(true);
            throw new exception("DBManager:update, SQL error: ".mysql_error($this->connection)."\n sql: $sql", mysql_errno($this->connection));
        }

            
    }
	
	private function found($sql){
		$result = $this->query($sql);
		
		if ($result != null && sizeof($result)>0)
			return true;
		
		return false;
	}
    
    public function startTransaction(){
		mysql_query("SET AUTOCOMMIT=0", $this->connection);
		mysql_query("START TRANSACTION", $this->connection);
        //mysql_autocommit($this->connection, FALSE);
    }
    
    public function endTransaction($rollback = false){
        if ($rollback == true){
			mysql_query("ROLLBACK", $this->connection);
            //mysql_rollback($this->connection); 
        }else{
            //mysql_commit($this->connection);
            mysql_query("COMMIT", $this->connection); 
            mysql_query("SET AUTOCOMMIT=1", $this->connection);
        }
        
    }
    
    public function close() {
		if(is_resource($this->connection)){
			mysql_close($this->connection);
		}
		
		
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
					
			
			$sql = "INSERT INTO `B5CGM`.`tac_playeringame` VALUES ( $gameid, $slot, $userid, $slot, 0, -3)";
			
			$this->insert($sql);
			
			
		}catch(Exception $e) {
            throw $e;
        }
	}
	
	public function createGame($name, $background, $maxplayers, $points, $userid){
	
		try{
			$sql = "INSERT INTO `B5CGM`.`tac_game` VALUES (	null,'".$this->DBEscape($name)."',0,-2,-1,'$background',$points, 'LOBBY', 2, $userid)";
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
                
                $sql = "INSERT INTO `B5CGM`.`tac_critical` VALUES(null, $gameid, ".$critical->shipid.", ".$critical->systemid.",'".$critical->phpclass."', $turn)";
    
                $this->update($sql);
            }
                
            
        }
        catch(Exception $e) {
            throw $e;
        }
    
    }
    
    public function getCriticals($shipid, $gameid, $systemid){
        $sql = "SELECT * FROM `B5CGM`.`tac_critical` WHERE gameid = $gameid AND shipid = $shipid AND systemid = $systemid";

        $criticals = array();
         
         try {
            $result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
                return $criticals;
                
                foreach ($result as $value) {
                    $entry = new $value->type($value->id, $value->shipid, $value->systemid, $value->type, $value->turn);
                    $criticals[] = $entry;
                }
           
            
            }
            catch(Exception $e) {
                throw $e;
            }

        return $criticals;
    
    }
    
    public function updateFireOrders($fireOrders){
    
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
        
        try {
            
            
            foreach ($fireOrders as $fire){
                if ($fire->turn != $turn)
                    continue;
                    
                if ($fire->type=="ballistic" && $phase != 1)
					continue;
					
				if ($fire->type!="ballistic" && $phase == 1)
					continue;
                            
                //$id, $shooterid, $targetid, $weaponid, $calledid, $turn, $firingmode, $needed = 0, $rolled = 0
                
                $sql = "INSERT INTO `B5CGM`.`tac_fireorder` VALUES (null, '".$fire->type."', ".$fire->shooterid.", ".$fire->targetid.", ".$fire->weaponid.", ".$fire->calledid.", ".$fire->turn.", "
                        .$fire->firingmode.", ". $fire->needed.", ".$fire->rolled.", $gameid, '".$fire->notes."', ".$fire->shotshit.", ".$fire->shots.", '".$fire->pubnotes."', 0, '".$fire->x."', '".$fire->y."')";

                $this->update($sql);
            }
                
            
        }
        catch(Exception $e) {
            throw $e;
        }
    
    }
    
    public function getFireorders($shipid, $gameid){
        $sql = "SELECT * FROM `B5CGM`.`tac_fireorder` WHERE gameid = $gameid AND shooterid = $shipid";

        $orders = array();
         
         try {
            $result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
                return $orders;
                
                foreach ($result as $value) {
                    $entry = new FireOrder($value->id, $value->type, $value->shooterid, $value->targetid, $value->weaponid, $value->calledid, $value->turn, $value->firingmode, $value->needed, $value->rolled, $value->shots, $value->shotshit, $value->intercepted, $value->x, $value->y);
                    $entry->notes = $value->notes;
                    $entry->pubnotes = $value->pubnotes;
                    $orders[] = $entry;
                }
           
            
            }
            catch(Exception $e) {
                throw $e;
            }
		
        return $orders;
    
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
    
    public function getPower($shipid, $gameid, $systemid){
        $sql = "SELECT * FROM `B5CGM`.`tac_power` WHERE gameid = $gameid AND shipid = $shipid AND systemid = $systemid";

        $powers = array();
         
         try {
            $result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
                return $powers;
                
                foreach ($result as $value) {
                                                     //$id, $shipid, $systemid, $type, $turn, $amount
                    $entry = new PowerManagementEntry($value->id, $value->shipid, $value->systemid, $value->type, $value->turn, $value->amount);
        
                    $powers[] = $entry;
                }
           
            
            }
            catch(Exception $e) {
                throw $e;
            }

        return $powers;
    
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
    
    public function getDamage($shipid, $gameid, $systemid){
        $sql = "SELECT * FROM `B5CGM`.`tac_damage` WHERE gameid = $gameid AND shipid = $shipid AND systemid = $systemid";

        $damages = array();
         
         try {
            $result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
                return $damages;
                
                foreach ($result as $value) {
                    $entry = new DamageEntry($value->id, $value->shipid, $value->gameid, $value->turn, $value->systemid, $value->damage, $value->armour, $value->shields, $value->fireorderid, $value->destroyed, $value->pubnotes);
        
                    $damages[] = $entry;
                }
           
            
            }
            catch(Exception $e) {
                throw $e;
            }

        return $damages;
    
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
            $sql = "UPDATE `B5CGM`.`tac_playeringame` SET `lastturn` = $turn, `lastphase` = $phase WHERE gameid = $gameid AND playerid = $userid";

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
    
	public function deploy($gameid, $shipid, $movement){
		try {
           	
			$preturn = ($movement->preturn) ? 1 : 0;
			
			$sql = "Insert into `B5CGM`.`tac_shipmovement` values (null, $shipid, $gameid, '".$movement->type."', ".$movement->x.", ".$movement->y.", ".$movement->xOffset.", ".$movement->yOffset.", ".$movement->speed.", ".$movement->heading.", ".$movement->facing.", $preturn, '".$movement->getReqThrustJSON()."', '".$movement->getAssThrustJSON()."', 0)";
			
			//throw new exception("sql: ".$movement->preturn . var_dump($movement));
			$this->insert($sql);
		
    
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
                
                $sql = "Insert into `B5CGM`.`tac_shipmovement` values (null, $shipid, $gameid, '".$movement->type."', ".$movement->x.", ".$movement->y.", ".$movement->xOffset.", ".$movement->yOffset.", ".$movement->speed.", ".$movement->heading.", ".$movement->facing.", $preturn, '".$movement->getReqThrustJSON()."', '".$movement->getAssThrustJSON()."', $turn)";
                
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
    
    
    public function getTacShips($gameid, $players, $turn, $phase){
        $sql = "select * from tac_ship s join tac_iniative i on s.id = i.shipid where s.tacgameid = $gameid and i.turn = $turn order by i.iniative asc";
        
        $ships = array();
         try {
            $result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
                return null;
                
            foreach ($result as $value) {
                                               
                $moves = $this->getMoves($value->id, $gameid, $turn);
                $EW = $this->getEW($value->id, $gameid, $turn);
                $fireOrders = $this->getFireOrders($value->id, $gameid);
        
                
                
                $ship = new $value->phpclass($value->id, $value->playerid, $value->name, $value->campaignX, $value->campaignY, $value->rolled, $value->rolling, $moves);
                
                foreach ($ship->systems as $system){
                    $system->damage = $this->getDamage($value->id, $gameid, $system->id);
                    $system->power = $this->getPower($value->id, $gameid, $system->id);
                    $system->setCriticals($this->getCriticals($ship->id, $gameid, $system->id));
					//$system->beforeTurn($ship, $turn, $phase);
                }
                
                $ship->EW = $EW;
                $ship->team = $players[$value->playerid]->team;
                $ship->iniative = $value->iniative;
                $ship->fireOrders = $fireOrders;
                
				$ships[] = $ship;
                
            }
            
            //
            
            }
            catch(Exception $e) {
                throw $e;
            }
            
        return $ships;
    }
    
    public function getEW($shipid, $gameid, $turn){
        $sql = "SELECT * FROM tac_ew where shipid = $shipid and gameid = $gameid and turn > ".($turn-3)." order by id ASC";
        $EW = array();
         
         try {
            $result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
                return $EW;
                
                foreach ($result as $value) {
                    $entry = new EWentry($value->id, $value->shipid, $value->turn, $value->type, $value->amount, $value->targetid);
        
                    $EW[] = $entry;
                }
           
            
            }
            catch(Exception $e) {
                throw $e;
            }

        return $EW;
    }
    
    
    public function getMoves($shipid, $gameid, $turn){
        $sql = "SELECT * FROM tac_shipmovement where shipid = $shipid and gameid = $gameid and ( turn > ".($turn-3)." OR turn = 1 ) order by id ASC";
				
        $moves = array();
         
         try {
            $result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
                return null;
                
            foreach ($result as $value) {
                $move = new MovementOrder($value->id, $value->type, $value->x, $value->y, $value->xOffset, $value->yOffset, $value->speed, $value->heading, $value->facing, $value->preturn, $value->turn);

                $move->setReqThrustJSON($value->requiredthrust);
                $move->setAssThrustJSON($value->assignedthrust);
            
                $moves[] = $move;
                
                }
           
            
            }
            catch(Exception $e) {
                throw $e;
            }

        return $moves;
    }
    
    
    
  
    
      
    
    public function authenticatePlayer($username, $password){
		$username = $this->DBEscape($username);	
		$password = $this->DBEscape($password);	
		
        $sql ="SELECT * FROM player where username = '$username' and password = password('$password')";
        $id = false;
        try {
            $result = $this->query($sql);
            $this->close();
            
            if ($result == null || sizeof($result) == 0)
                return false;
                
            $value = $result[0];   
            $id = $value->id;
            
           
        }
        catch(Exception $e) {
            throw $e;
        }
        
        return $id;
    }
    

    
    //UTILS
    
    public function chekcIfTableExists($name, $close = true){
        $sql = "show tables like '$name'";
        
        try {
            $result = $this->query($sql);
            
            if ($result == null || sizeof($result) == 0)
                return false;
                
            
            if ($close)
                $this->close();
            
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
            
            $this->close();
        }catch(Exception $e) {
            throw $e;
        }
    }
}
?>
