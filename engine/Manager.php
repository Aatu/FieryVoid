<?php
class Manager{

    private static $dbManager = null;

    private static function initDBManager() {
        if (self::$dbManager == null)
            self::$dbManager = new DBManager("localhost", 3306, "B5CGM", "aatu", "Kiiski");
    }
    
    public static function leaveLobbySlot($user){
        try {
            self::initDBManager();
            self::$dbManager->leaveSlot($user);
            
        }
        catch(exception $e) {
            throw $e;
        }
    
    }
    
    public static function getGameLobbyData($userid, $targetGameId = false){
        try {
            self::initDBManager();
            
            if ($targetGameId &&  is_numeric($targetGameId) && $targetGameId > 0 )
                return self::getTacGamedata($targetGameId, $userid, 0, 0, -1);
            
            $gameid = self::$dbManager->shouldBeInGameLobby($userid);
            if ($gameid == false)
                return null;
                
            
                                
            return self::getTacGamedata($gameid, $userid, 0, 0, -1);
        }
        catch(exception $e) {
            throw $e;
        }
    }
    
    public static function getTacGames($userid){
        $gamedata = null;
        
        if (!is_numeric($userid))
			return null;
        
        try {
            self::initDBManager();
            $games = self::$dbManager->getTacGames($userid);
            if ($games == null)
                return null;
            
            foreach ($games as $game)
                $game->prepareForPlayer(0, 0, -1);
    
        }
        catch(exception $e) {
            throw $e;
        }
        
        return $games;
    }
    
    public static function shouldBeInGame($userid){
		if (!is_numeric($userid))
			return null;
			
        try {
            self::initDBManager();
            return self::$dbManager->shouldBeInGameLobby($userid);
        }
        catch(exception $e) {
            throw $e;
        }
    }
    
    public static function createGame($name, $bg, $maxplayers, $points, $userid){
    
        try {
            self::initDBManager();
            $gameid = self::$dbManager->createGame($name, $bg, $maxplayers, $points, $userid);
            self::takeSlot($userid, $gameid, 1);
            
            return $gameid;
        }
        catch(exception $e) {
            throw $e;
        }
    
    }
    
    public static function takeSlot($userid, $gameid, $slot){
        
        try {
            self::initDBManager();
            //self::$dbManager->startTransaction();
            return self::$dbManager->takeSlot($userid, $gameid, $slot);
            //self::$dbManager->endTransaction();
            
        }
        catch(exception $e) {
            throw $e;
        }
        
    }
    
    public static function getMapBackgrounds(){
        $handle = opendir("./maps/");
        $list = array();
        while (false !== ($entry = readdir($handle))) {
        
            if (preg_match("/.*\.(bmp|jpeg|gif|png|jpg)$/i", $entry))
                $list[] = $entry;
        }
        
        return $list;
    }
    
    public static function getAllShips(){
        
        return ShipLoader::getAllShips();
    }
    
    public static function canCreateGame($userid){
        if (self::shouldBeInGame($userid))
            return false;
        
        return true;
    }
    
    public static function authenticatePlayer($username, $password) {
        try {
            self::initDBManager();
            $ret =  self::$dbManager->authenticatePlayer($username, $password);
                      
            return $ret;
        }
        catch(exception $e) {
            throw $e;
        }
        
    }
    
    public static function getTacGamedata($gameid, $userid, $turn, $phase, $activeship){
    
		if (!is_numeric($gameid) || !is_numeric($userid) || !is_numeric($turn) || !is_numeric($phase) || !is_numeric($activeship) )
			return null;
		
        
        
    
        $gamedata = null;
        try {
            self::initDBManager();
            if (self::$dbManager->isNewGamedata($gameid, $turn, $phase, $activeship)){
                $gamedata = self::$dbManager->getTacGamedata($userid, $gameid);
                if ($gamedata == null)
                    return null;
                //print(var_dump($gamedata));
                $gamedata->prepareForPlayer($turn, $phase, $activeship);
            }else{
                return null;
            }
        }
        catch(exception $e) {
            throw $e;
        }
        
        return $gamedata;
        
        
    }
    
    public static function getTacGamedataJSON($gameid, $userid, $turn, $phase, $activeship){
        
        $gdS = self::getTacGamedata($gameid, $userid, $turn, $phase, $activeship);
        
        if (!$gdS)
            return "{}";
        
        if ($gdS->waiting && !$gdS->changed && $gdS->status != "LOBBY")
            return "{}";
        
                
        $json = json_encode($gdS, JSON_NUMERIC_CHECK);
    
       
        
        return $json;
    
    }
            
    public static function submitTacGamedata($gameid, $userid, $turn, $phase, $activeship, $ships){
        try {
            //file_put_contents('/tmp/fierylog', "Gameid: $gameid submitTacGamedata ships:". var_export($ships, true) ."\n\n", FILE_APPEND);
            $ships = self::getShipsFromJSON($ships);
            
            if (sizeof($ships)==0)
				return "{error:\"Error, No gamedata submitted.\"}";
            //print(var_dump($ships));
            $gamedata = new TacGamedata($gameid, $turn, $phase, $activeship, $userid, "", "", 0, "", 0);
            $gamedata->ships = $ships;
            
            self::initDBManager();  
            self::$dbManager->startTransaction();
            
            $gdS = self::$dbManager->getTacGamedata($userid, $gameid);
            
            if ($gameid != $gdS->id || $turn != $gdS->turn || $phase != $gdS->phase)
                return "{error:\"Error, we were not expecting this.\"}";
                
            if ($gdS->hasAlreadySubmitted($userid))
                return "{error: \"Error, turn already submitted. Or player not found\"}";
                
            if ($gdS->status == "FINISHED")
                return "{error: \"Error, game is finished\"}";
            
            //print(var_dump($ships));
            
            if ($gdS->phase == 1){
                 if (!($ret = self::handleInitialActions($ships, $gdS)))
                        return $ret;
                        
            }else if ($gdS->phase == 2){
                if ($activeship == $gdS->activeship){
                    if (!($ret = self::handleMovement($ships, $gdS)))
                        return $ret;
                }else{
                    return "{error: \"phase and active ship does not match\"}";
                }
            }else if ($gdS->phase == 3){
                if (!($ret = self::handleFiringOrders($ships, $gdS)))
                    return $ret;
            }else if ($gdS->phase == 4){
                if (!($ret = self::handleFinalOrders($ships, $gdS)))
                    return $ret;
            }else if ($gdS->phase == -2){
                if (!($ret = self::handleBuying($ships, $gdS)))
                    return $ret;
            }
            
            $gdS = self::$dbManager->getTacGamedata($userid, $gameid);
            $gdS->prepareForPlayer($turn, $phase, $activeship);
                      
            $gdS = json_encode($gdS, JSON_NUMERIC_CHECK);
            
            self::$dbManager->endTransaction(false);
                   
            return $gdS;
            
        }catch(exception $e) {
            self::$dbManager->endTransaction(true);
            return $e->getMessage();
        }
        
        return "error";
    }
    
    private static function handleBuying(  $ships, $gamedata ){
    

        $points = 0;
        foreach ($ships as $ship){
            $points += $ship->pointCost;
        }
        
        if ($points > $gamedata->points)    
            return "Error, fleet too expensive.";
    
            
        foreach ($ships as $ship){
            if ($ship->userid == $gamedata->forPlayer){
                self::$dbManager->submitShip($gamedata->id, $ship, $gamedata->forPlayer);
            }
        }
        
    
        self::$dbManager->updatePlayerStatus($gamedata->id, $gamedata->forPlayer, $gamedata->phase, $gamedata->turn);
        
        if (sizeof($gamedata->players)<2){
            return true;
        }
        
        if ($gamedata->othersDone($gamedata->forPlayer)){
            $servergamedata = self::$dbManager->getTacGamedata($gamedata->forPlayer, $gamedata->id);
            self::startGame($servergamedata);
        
        }
        
        return true;
        
    }
    
    
    private static function handleFinalOrders(  $ships, $gamedata ){
        self::$dbManager->updatePlayerStatus($gamedata->id, $gamedata->forPlayer, $gamedata->phase, $gamedata->turn);
        
        if ($gamedata->othersDone($gamedata->forPlayer)){
            self::changeTurn($gamedata);
        
        }
        
        return true;
    }
    
    private static function handleFiringOrders( $ships, $gamedata ){
    
        foreach ($ships as $ship){
            if ($ship->userid != $gamedata->forPlayer)  
                continue;
            
            if ($ship->isDestroyed())
                continue;
            
            if (Movement::validateMovement($gamedata, $ship)){
                if (count($ship->movement)>0)   
                    self::$dbManager->submitMovement($gamedata->id, $ship->id, $gamedata->turn, $ship->movement);
            }
            
            if (Firing::validateFireOrders($ship->fireOrders, $gamedata)){
                self::$dbManager->submitFireorders($gamedata->id, $ship->fireOrders, $gamedata->turn, $gamedata->phase);
            }
            
        }
        
        
        self::$dbManager->updatePlayerStatus($gamedata->id, $gamedata->forPlayer, $gamedata->phase, $gamedata->turn);
        
        if ($gamedata->othersDone($gamedata->forPlayer)){
            self::startEndPhase($gamedata);
        
        }
        //print("firing");
        return true;
    
    }
    
    private static function handleInitialActions( $ships, $gamedata ){
    
        foreach ($ships as $ship){
            if ($ship->userid != $gamedata->forPlayer)  
                continue;
                
            $powers = array();
            
            foreach ($ship->systems as $system){
                $powers = array_merge($powers, $system->power);
            }
        
            self::$dbManager->submitPower($gamedata->id, $gamedata->turn, $powers);
        }
        
        $gd = self::$dbManager->getTacGamedata($gamedata->forPlayer, $gamedata->id);
        
        
        foreach ($ships as $ship){
            if ($ship->userid != $gamedata->forPlayer)  
                continue;
            
            
            
            if (EW::validateEW($ship->EW, $gd)){
                self::$dbManager->submitEW($gamedata->id, $ship->id, $ship->EW, $gamedata->turn);
            }else{
                return "{error: \"Failed to validate EW\"}";
            }
		   			
            
            
            
        }
		
		$gd = self::$dbManager->getTacGamedata($gamedata->forPlayer, $gamedata->id);
        
        
        foreach ($ships as $ship){
            if ($ship->userid != $gamedata->forPlayer)  
                continue;
          		            
            if (Firing::validateFireOrders($ship->fireOrders, $gd)){
				 self::$dbManager->submitFireorders($gamedata->id, $ship->fireOrders, $gamedata->turn, $gamedata->phase);
            }else{
                return "{error: \"Failed to validate Ballistic firing orders\"}";
            }
			
            
            
            
        }
        
        self::$dbManager->updatePlayerStatus($gamedata->id, $gamedata->forPlayer, $gamedata->phase, $gamedata->turn);
        
        if ($gamedata->othersDone($gamedata->forPlayer)){
            self::startMovement($gamedata);
        
        }
        
        return true;
    
    }
    
    private static function startMovement($gamedata){
    
        $gamedata->phase = 2; 
        $gamedata->activeship = $gamedata->getFirstShip()->id;
        self::$dbManager->updateGamedata($gamedata);
    
    }
    
    private static function startWeaponAllocation($gamedata){
        $gamedata->phase = 3; 
        $gamedata->activeship = -1;
        self::$dbManager->updateGamedata($gamedata);
    }
    
    private static function startGame($gamedata){
                    
        $servergamedata = self::$dbManager->getTacGamedata($gamedata->forPlayer, $gamedata->id);
        
        
        $t1 = 0;
        $t2 = 0;
        
        foreach ($servergamedata->ships as $ship){
            
            $player = $servergamedata->getPlayerById($ship->userid);
            $y = 0;
            $t = 0;
            $h = 3;
            if ($player->team == 1){
                $t1++;
                $t = $t1;
                $h = 0;
            }else{
                $t2++;
                $t = $t2;
            }
            
            if ($t % 2 == 0){
                $y = $t/2;
            }else{
                $y = (($t-1)/2)*-1;
            }
            
            $x = -30;
            
            if ($player->team == 2){
                $x=30;
            }
            
            
            
            $move = new MovementOrder(-1, "start", $x, $y, 0, 0, 5, $h, $h, true, 1, 0);
            self::$dbManager->deploy($servergamedata->id, $ship->id, $move);
            
        }
        
        self::changeTurn($gamedata);
    }
    
    private static function startEndPhase($gamedata){
        //print("start end");
        $gamedata->phase = 4; 
        $gamedata->activeship = -1;
        self::$dbManager->updateGamedata($gamedata);
        
        $servergamedata = self::$dbManager->getTacGamedata($gamedata->forPlayer, $gamedata->id);
        Firing::automateIntercept($servergamedata);
        Firing::fireWeapons($servergamedata);
        $criticals = Criticals::setCriticals($servergamedata);
		//var_dump($servergamedata->getNewFireOrders());
		//throw new Exception();
		self::$dbManager->submitFireorders($servergamedata->id, $servergamedata->getNewFireOrders(), $servergamedata->turn, 3);
        self::$dbManager->updateFireOrders($servergamedata->getUpdatedFireOrders());
        self::$dbManager->submitDamages($servergamedata->id, $servergamedata->turn, $servergamedata->getNewDamages());
        self::$dbManager->submitCriticals($servergamedata->id,  $servergamedata->getUpdatedCriticals(), $servergamedata->turn);
    }
    
    private static function handleMovement( $ships, $gamedata ){
    
        $turn = $gamedata->getActiveship()->getLastTurnMoved();
        if ($gamedata->turn <= $turn)
            return "Already moved";
        
            
        self::$dbManager->submitMovement($gamedata->id, $ships[$gamedata->activeship]->id, $gamedata->turn, $ships[$gamedata->activeship]->movement);
        
        $next = false;
        $nextshipid = -1;
        $firstship = null;
        foreach ($gamedata->ships as $ship){
            if ($firstship == null)
                $firstship = $ship;
                        
            if ($next && !$ship->isDestroyed()){
                $nextshipid = $ship->id;
                break;
            }
            
            if ($ship->id == $gamedata->activeship)
                $next = true;
        }
        
        if ($nextshipid > -1){
            $gamedata->activeship = $nextshipid;
            self::$dbManager->updateGamedata($gamedata);
        }else{
            self::startWeaponAllocation($gamedata);
            
        }
        
        
        
        return true;
    }
    
    private static function changeTurn($gamedata){
    
        $gamedata->turn = $gamedata->turn+1;
        $gamedata->phase = 1; 
        $gamedata->activeship = -1;
        $gamedata->status = "ACTIVE";
        
        if ($gamedata->isFinished())
            $gamedata->status = "FINISHED";
            
        self::generateIniative($gamedata);
        self::$dbManager->updateGamedata($gamedata);
        
               
        $servergamedata = self::$dbManager->getTacGamedata($gamedata->forPlayer, $gamedata->id);
        
        foreach ($servergamedata->ships as $key=>$ship){
            $movement = Movement::setPreturnMovementStatusForShip($ship, $servergamedata->turn);
            self::$dbManager->submitMovement($servergamedata->id, $ship->id, $servergamedata->turn, $movement, true);
        }
            
    }
    
    private static function generateIniative($gamedata){
        foreach ($gamedata->ships as $key=>$ship){
            $mod = 0;
            $speed = $ship->getSpeed();
        
            if ( $speed < 5){
                $mod = (5-$speed)*10;
            }
            
            $CnC = $ship->getSystemByName("CnC");
            
            if ($CnC){
				$mod += 5*($CnC->hasCritical("CommunicationsDisrupted", $gamedata->turn));
				$mod += 10*($CnC->hasCritical("ReducedIniativeOneTurn", $gamedata->turn));
				$mod += 10*($CnC->hasCritical("ReducedIniative", $gamedata->turn));
			}
            
            
                        
            $ship->iniative = Dice::d(100) + $ship->iniativebonus - $mod;
        }
        self::$dbManager->submitIniative($gamedata->id, $gamedata->turn, $gamedata->ships);
        
    }
    
    private static function getShipsFromJSON($json){
        $ships = array();
        $array = json_decode($json, true);
        if (!is_array($array))
			return $ships;
			
        foreach ($array as $value) {
                    
            $movements = array();
            if (is_array($value["movement"])){
                foreach($value["movement"] as $i=>$move){
                    $movement = new MovementOrder(
						$move["id"],
						$move["type"],
						$move["x"],
						$move["y"],
						$move["xOffset"], 
						$move["yOffset"], 
						$move["speed"], 
						$move["heading"], 
						$move["facing"], 
						$move["preturn"], 
						$move["turn"],
						$move["value"]
					);
                    $movement->requiredThrust = $move["requiredThrust"];
                    $movement->assignedThrust = $move["assignedThrust"];
                    
                    $movements[$i] = $movement;
                }
            }
            
            $EW = array();
            
            if (is_array($value["EW"])){
                foreach($value["EW"] as $i=>$EWdata){
                    $EWentry = new EWentry(-1, $EWdata["shipid"], $EWdata["turn"], $EWdata["type"], $EWdata["amount"], $EWdata["targetid"]);
                    $EW[$i] = $EWentry;
                }
            }
            
            $fireOrders = array();
            
            if (is_array($value["fireOrders"])){
                foreach($value["fireOrders"] as $i=>$fo){
                                                //$id, $shooterid, $targetid, $weaponid, $calledid, $turn, $firingmode
                    $fireOrder = new FireOrder(-1, $fo["type"], $fo["shooterid"], $fo["targetid"], $fo["weaponid"], $fo["calledid"], $fo["turn"], $fo["firingmode"], 0, 0, $fo["shots"], 0, 0, $fo["x"], $fo["y"]);
                    $fireOrders[$i] = $fireOrder;
                }
            }
            
            
            
            
            $ship = new $value["phpclass"]($value["id"], $value["userid"], $value["name"], $movements );
                
            $ship->EW = $EW;
            $ship->fireOrders = $fireOrders;
            
            foreach($value["systems"] as $i=>$system){
                if (!is_array($system["power"]))
                    continue;
                    
                foreach ($system["power"] as $a=>$power){
                                                    
                    $powerEntry = new PowerManagementEntry($power["id"], $power["shipid"], $power["systemid"], $power["type"], $power["turn"], $power["amount"]);
                    $sys = $ship->getSystemById($powerEntry->systemid);
                    
                    if (isset($sys)){
                        $sys->power[] = $powerEntry;
                    }
                }
            
            }
            
            $ships[(int)$value["id"]] = $ship;
        }
        
        return $ships;
    }
    
    
}

?>
