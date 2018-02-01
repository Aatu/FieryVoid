<?php

require_once dirname(__DIR__) . '/varconfig.php'; 

set_error_handler(
    function ($errno, $errstr, $file, $line)
    {
        throw new ErrorException($errstr, $errno, 1, $file, $line);
    }
);

class Manager{

    private static $dbManager = null;

    /**
     *  @return DBManager dbManager
     */
    private static function initDBManager() {
    	global $database_name;
    	global $database_user;
    	global $database_password;
        if (self::$dbManager == null)
            self::$dbManager = new DBManager("localhost", 3306, $database_name, $database_user, $database_password);
    }
    
    private static function deleteOldGames()
    {
        try {
            self::initDBManager();
            self::$dbManager->startTransaction();
            $ids = self::$dbManager->getGamesToBeDeleted();
            self::$dbManager->deleteGames($ids);
                
            self::$dbManager->endTransaction(false);
        }catch(exception $e) {
            self::$dbManager->endTransaction(true);
            throw $e;
        }
    }
    
    public static function leaveLobbySlot($user, $gameid, $slotid = null){
        try {
            self::initDBManager();
            self::$dbManager->leaveSlot($user, $gameid, $slotid);
            self::$dbManager->deleteEmptyGames();
        }
        catch(exception $e) {
            throw $e;
        }
    
    }
    
    public static function getGameLobbyData($userid, $targetGameId = false){
        try {
            self::initDBManager();
            
            if ($targetGameId &&  is_numeric($targetGameId) && $targetGameId > 0 ){
                return self::getTacGamedata($targetGameId, $userid, 0, 0, -1);
            }
        }
        catch(Exception $e) {
            Debug::error($e);
        }
    }
    
    public static function getTacGames($userid){
        
        if (!is_numeric($userid))
			return null;
        
        try {
            self::initDBManager();
            
            $games = self::$dbManager->getTacGames($userid);

            if ($games == null){
                return null;
            }
            
            
            foreach ($games as $game){
                $game->prepareForPlayer(0, 0, -1);
            }
        }
        catch(exception $e) {
            throw $e;
        }
        
        return $games;
    }


    public static function getFirePhaseGames($userid){


        try {
            self::initDBManager();
            
            $games = self::$dbManager->getFirePhaseGames($userid);

            if ($games == null){
                return null;
            }            
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
    
    public static function createGame($userid, $data){
        $data = json_decode($data, true);
        
        $gamename = $data["gamename"];
        $background = $data["background"];
        $gamespace = $data["gamespace"];
        $slots = array();
        $pointsA = $data["slots"][0]["points"];
        $poinstB = $data["slots"][1]["points"];

        debug::log($pointsA);
        debug::log($poinstB);
        
        foreach ($data["slots"] as $slot){
            $slots[] = new PlayerSlotFromJSON($slot);
        }
        
        try {
            self::initDBManager();
            self::$dbManager->startTransaction();
            $gameid = self::$dbManager->createGame($gamename, $background, $slots, $userid, $gamespace);
            self::takeSlot($userid, $gameid, 1);
            self::$dbManager->endTransaction(false);
            return $gameid;
        }
        catch(exception $e) {
            self::$dbManager->endTransaction(true);
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
        $handle = opendir("img/maps/");
        $list = array();
        while (false !== ($entry = readdir($handle))) {
        
            if (preg_match("/.*\.(bmp|jpeg|gif|png|jpg)$/i", $entry))
                $list[] = $entry;
        }
        
        return $list;
    }
    
    public static function getAllFactions(){
    	return ShipLoader::getAllFactions();
    }
    
    public static function getAllShips(){
        
        //return ShipLoader::getAllShips();
        return array();
    }
    
    public static function canCreateGame($userid){
        return true;
    }
    public static function registerPlayer($username, $password) {
        try {
            self::initDBManager();
            $ret =  self::$dbManager->registerPlayer($username, $password);
                      
            return $ret;
        }
        catch(exception $e) {
            Debug::error($e);
            return null;
        }
    }
    
    public static function changePassword($username, $passwordold, $passwordnew) {
        try {
            self::initDBManager();
            $ret =  self::$dbManager->changePassword($username, $passwordold, $passwordnew);
                      
            return $ret;
        }
        catch(exception $e) {
            Debug::error($e);
            return null;
        }
        
    }
    
    
    public static function authenticatePlayer($username, $password) {
        try {
            self::initDBManager();
            $ret =  self::$dbManager->authenticatePlayer($username, $password);
                      
            return $ret;
        }
        catch(exception $e) {
            Debug::error($e);
            return null;
        }
        
    }
    
    public static function getTacGamedata($gameid, $userid, $turn, $phase, $activeship){
    
	if (!is_numeric($gameid) || !is_numeric($userid) || !is_numeric($turn) || !is_numeric($phase) || !is_numeric($activeship) )
                return null;
        
        $gamedata = null;
        
        self::initDBManager();

        if ($turn === -1)
            self::deleteOldGames ();

        self::advanceGameState($userid, $gameid);

        if (self::$dbManager->isNewGamedata($gameid, $turn, $phase, $activeship)){
            //Debug::log("GAME: $gameid Player: $userid requesting gamedata, new found.");
            $gamedata = self::$dbManager->getTacGamedata($userid, $gameid);
            if ($gamedata == null)
                return null;
            //print(var_dump($gamedata));
            $gamedata->prepareForPlayer($turn, $phase, $activeship);
        }else{
            return null;
        }
        
        return $gamedata;
    }
    
    public static function updateAmmoInfo($shipid, $systemid, $gameid, $firingmode, $ammoAmount){
        self::$dbManager->updateAmmoInfo($shipid, $systemid, $gameid, $firingmode, $ammoAmount);
    }

    
    public static function getTacGamedataJSON($gameid, $userid, $turn, $phase, $activeship){
        
        try{
            $gdS = self::getTacGamedata($gameid, $userid, $turn, $phase, $activeship);

            if (!$gdS)
                return "{}";

            if ($gdS->waiting && !$gdS->changed && $gdS->status != "LOBBY")
                return "{}";


            $json = json_encode($gdS, JSON_NUMERIC_CHECK);


            //Debug("GAME: $gameid Player: $userid requesting gamedata. RETURNING NEW JSON");
            return $json;

        }
        catch(Exception $e) {
            $logid = Debug::error($e);
            return '{"error": "' .$e->getMessage() . '", "code":"'.$e->getCode().'", "logid":"'.$logid.'"}';
        }
    
    }
            
    public static function submitTacGamedata($gameid, $userid, $turn, $phase, $activeship, $ships, $status, $slotid = 0){
        try {
        
            //    file_put_contents('/tmp/fierylog', "Gameid: $gameid submitTacGamedata ships:". var_export($ships, true) ."\n\n", FILE_APPEND);
            self::initDBManager();  
            $starttime = time();
	    $gdS = self::$dbManager->getTacGamedata($userid, $gameid); //Marcin Sawicki: moving so TacGamedata is available when ships from JSON are loaded!
            $ships = self::getShipsFromJSON($ships, $gameid);
		
            if (sizeof($ships)==0) throw new Exception("Gamedata missing");
            //print(var_dump($ships));
            //$gamedata = new TacGamedata($gameid, $turn, $phase, $activeship, $userid, "", "", 0, "", 0);
            //$gamedata->ships = $ships;
            
            if (!self::$dbManager->getPlayerSubmitLock($gameid, $userid))
                throw new Exception("Failed to get player lock");
            
            //Debug("GAME: $gameid Player: $userid starting submit of phase $phase");
            
            self::$dbManager->startTransaction();
            
	    //Marcin Sawicki: let's try to mpove this earlier!	
            //$gdS = self::$dbManager->getTacGamedata($userid, $gameid);
            
            if($status == "SURRENDERED"){
                self::$dbManager->updateGameStatus($gameid, $status);
            }
            
            if ($gameid != $gdS->id || $turn != $gdS->turn || $phase != $gdS->phase)
                throw new Exception("Unexpected orders");
                
            if ($gdS->hasAlreadySubmitted($userid))
                throw new Exception("Turn already submitted or wrong user");
                
            if ($gdS->status == "FINISHED")
                throw new Exception("Game is finished");
            
            //print(var_dump($ships));
            
            if ($gdS->phase == 1){
                 $ret = self::handleInitialActions($ships, $gdS);		    
            }else if ($gdS->phase == 2){
                if ($activeship == $gdS->activeship){
                    $ret = self::handleMovement($ships, $gdS);
                }else{
                    throw new Exception("phase and active ship does not match");
                }
            }else if ($gdS->phase == 3){
                $ret = self::handleFiringOrders($ships, $gdS);
            }else if ($gdS->phase == 4){		    
                $ret = self::handleFinalOrders($ships, $gdS);
            }else if ($gdS->phase == -2){
                $ret = self::handleBuying($ships, $gdS, $slotid);
            }else if ($gdS->phase == -1){
                $ret = self::handleDeployment($ships, $gdS);
            }
                        
            self::$dbManager->endTransaction(false);
            
            self::$dbManager->releasePlayerSubmitLock($gameid, $userid);
            
            //Debug("GAME: $gameid Player: $userid SUBMIT OK");
            
            $endtime = time();
            //Debug::log("SUBMITTING GAMEDATA - GAME: $gameid Time: " . ($endtime - $starttime) . " seconds.");
            return '{}';
            
        }catch(exception $e) {
            self::$dbManager->endTransaction(true);
            self::$dbManager->releasePlayerSubmitLock($gameid, $userid);
            $logid = Debug::error($e);
            return '{"error": "' .$e->getMessage() . '", "code":"'.$e->getCode().'", "logid":"'.$logid.'"}';
        }
       
        
    }
    
    private static function handleBuying( $ships, $gamedata, $slotid ){
    
        $seenSlots = array();
        foreach($gamedata->slots as $slot)
        {
            if ($gamedata->hasAlreadySubmitted($gamedata->forPlayer, $slot->slot))
                continue;
            
            $points = 0;
            foreach ($ships as $ship){

                if ($ship->slot != $slot->slot)
                    continue;
                
                $seenSlots[$slot->slot] = true;

                if (!$ship instanceof FighterFlight){
                    $points += $ship->pointCost;
                }


                else {
                    $points += ($ship->pointCost / 6) * $ship->flightSize;
                }

                if ($ship->userid == $gamedata->forPlayer){
                    $id = self::$dbManager->submitShip($gamedata->id, $ship, $gamedata->forPlayer);
                    
                    // Check if ship uses variable flight size
                    if($ship instanceof FighterFlight){
                        self::$dbManager->submitFlightSize($gamedata->id, $id, $ship->flightSize);

                        $firstFighter = $ship->systems[1];
                        $ammo = false;

                        foreach ($firstFighter->systems as $weapon){
                            if(isset($weapon->missileArray)){
                                $ammo = $weapon->missileArray[1]->amount;
                                break;
                            }
                        }

                        if ($ammo){
                            foreach($ship->systems as $fighter){
                                foreach ($fighter->systems as $weapon){
                                    if(isset($weapon->missileArray)){
                                        $weapon->missileArray[1]->amount = $ammo;
                                        self::$dbManager->submitAmmo($id, $weapon->id, $gamedata->id, $weapon->firingMode, $ammo);
                                    }
                                }
                            }
                        }
			else{//Marcin Sawicki: generalized version of gun ammo initialization for fighters (not for missile launchers!)
			   foreach($ship->systems as $fighter){
                               foreach($fighter->systems as $weapon){
                                   if(isset($weapon->ammunition) && (!isset($weapon->missileArray)) && ($weapon->ammunition > 0) ){
                                       self::$dbManager->submitAmmo($id, $weapon->id, $gamedata->id, $weapon->firingMode, $weapon->ammunition);
                                   }
                               }
                           }
			}
			/*Marcin Sawicki: let's generalize this, not limit to Templar!
                        else if ($ship instanceof Templar){
                            foreach($ship->systems as $fighter){
                               foreach($fighter->systems as $weapon){
                                   if($weapon instanceof PairedGatlingGun){
                                       self::$dbManager->submitAmmo($id, $weapon->id, $gamedata->id, $weapon->firingMode, $weapon->ammunition);
                                   }
                               }
                           }
                        }
			*/
                    }
                    else{
                        if (isset($ship->adaptiveArmour)){
                            self::$dbManager->submitAdaptiveArmour($gamedata->id, $id);
                        }

                       foreach($ship->systems as $systemIndex=>$system){
                               if(isset($system->missileArray)){
                                   // this system has a missileArray. It uses ammo
                                   foreach($system->missileArray as $firingMode=>$ammo){
                                       self::$dbManager->submitAmmo($id, $system->id, $gamedata->id, $firingMode, $ammo->amount);
                               }
                           }
                       }  
                    }
                }
            }

            if ($points > $slot->points)
            throw new Exception("Fleet too expensive.");
        }
    
        self::$dbManager->updatePlayerStatus($gamedata->id, $gamedata->forPlayer, $gamedata->phase, $gamedata->turn, $seenSlots);
        
        return true;
    }
    
    
    private static function handleFinalOrders(  $ships, $gamedata ){
        self::$dbManager->updatePlayerStatus($gamedata->id, $gamedata->forPlayer, $gamedata->phase, $gamedata->turn);
       
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
            
            if (Firing::validateFireOrders($ship->getAllFireOrders(), $gamedata)){
                self::$dbManager->submitFireorders($gamedata->id, $ship->getAllFireOrders(), $gamedata->turn, $gamedata->phase);
            }
            
        }
        
        
        self::$dbManager->updatePlayerStatus($gamedata->id, $gamedata->forPlayer, $gamedata->phase, $gamedata->turn);
        
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
                throw new Exception("Failed to validate EW");
            }
        }          
	    
	    
        foreach ($ships as $ship){
            if ($ship instanceof WhiteStar){
                self::$dbManager->updateAdaptiveArmour($gamedata->id, $ship->id, $ship->armourSettings);
            }
        }
		
	$gd = self::$dbManager->getTacGamedata($gamedata->forPlayer, $gamedata->id); //MJS: is it really necessary? $gd is created a few lines above in the same manner... leaving for now
        
        
        foreach ($ships as $ship){
            if ($ship->userid != $gamedata->forPlayer) continue;
		
            if (Firing::validateFireOrders($ship->getAllFireOrders(), $gd)){
		 self::$dbManager->submitFireorders($gamedata->id, $ship->getAllFireOrders(), $gamedata->turn, $gamedata->phase);    
            }else{
                throw new Exception("Failed to validate Ballistic firing orders");
            }
        }
	    
        self::$dbManager->updatePlayerStatus($gamedata->id, $gamedata->forPlayer, $gamedata->phase, $gamedata->turn);
                
        return true;    
    
    }
    
    public static function advanceGameState($playerid, $gameid){
        try{
            if (!self::$dbManager->checkIfPhaseReady($gameid))
                return;
		
            if (!self::$dbManager->getGameSubmitLock($gameid))
            {
                //Debug::log("Advance gamestate, Did not get lock. playerid: $playerid");
                return;
            }
            
            $starttime = time();
            
            //Debug("GAME: $gameid Starting to advance gamedata. playerid: $playerid");
            
            self::$dbManager->startTransaction();
		
            $gamedata = self::$dbManager->getTacGamedata($playerid, $gameid);
   			
            $phase = $gamedata->phase;
            
            if ($phase == 1){
                 self::startMovement($gamedata);
            }else if ($phase == 2){
                //Because movement does not have simultaenous orders, this is handled in handleMovement
            }else if ($phase == 3){
                   self::startEndPhase($gamedata);
            }else if ($phase == 4){
                self::changeTurn($gamedata);
            }else if ($phase == -2){
                self::startGame($gamedata);
            }else if ($phase == -1){
                self::startInitialOrders($gamedata);
            }
            
            if (TacGamedata::$currentPhase > 0){
		    /*if(TacGamedata::$currentGameID == 3578){ //DEBUG
			    print('before advancing systems data!');
		    }*/
                foreach ($gamedata->ships as $ship){
                    foreach ($ship->systems as $system){
                        $system->onAdvancingGamedata($ship);
                    }
                }
                
                self::$dbManager->updateSystemData(SystemData::$allData);
            }
            self::$dbManager->endTransaction(false);
            self::$dbManager->releaseGameSubmitLock($gameid);
            
            $endtime = time();
        }
        catch(Exception $e)
        {
            self::$dbManager->endTransaction(true);
            self::$dbManager->releaseGameSubmitLock($gameid);
            throw $e;
        }
    }
    
    private static function startInitialOrders($gamedata){
    
        $gamedata->setPhase(1); 
        
        self::$dbManager->updateGamedata($gamedata);
    
    }
    
    private static function startMovement($gamedata){
        $gamedata->setPhase(2); 

        $ship = $gamedata->getFirstShip();

        if ($ship instanceof StarBase){
            if ($gamedata->turn > 1){
                $moves = sizeof($ship->movement);
                debug::log($ship->movement[$moves-1]->type);
            }
        }

        $gamedata->setActiveship($gamedata->getFirstShip()->id);
        self::$dbManager->updateGamedata($gamedata);
    
    }
    
    private static function startWeaponAllocation($gamedata){
        $gamedata->setPhase(3); 
        $gamedata->setActiveship(-1);
        self::$dbManager->updateGamedata($gamedata);
    }
    
    private static function startGame($gamedata){
                    
        $servergamedata = self::$dbManager->getTacGamedata($gamedata->forPlayer, $gamedata->id);
        
        
        $t1 = 0;
        $t2 = 0;
        
        foreach ($servergamedata->ships as $ship){
            
            $y = 0;
            $t = 0;
            $h = 3;
            if ($ship->team == 1){
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
            
            if ($ship->team == 2){
                $x=30;
            }
            
            
            
            $move = new MovementOrder(-1, "start", $x, $y, 0, 0, 5, $h, $h, true, 1, 0, 0);
            $ship->movement = array($move);
            
            foreach ($ship->systems as $system)
            {
                $system->setInitialSystemData($ship);
            }
            
        }
        
        self::$dbManager->insertShips($servergamedata->id, $servergamedata->ships);
        self::$dbManager->insertSystemData(SystemData::$allData);
        
        self::changeTurn($gamedata);
    }
    
	
    private static function startEndPhase($gamedata){
        //print("start end");

        $gamedata->setPhase(4); 
        $gamedata->setActiveship(-1);

        self::$dbManager->updateGamedata($gamedata);
        
        $servergamedata = self::$dbManager->getTacGamedata($gamedata->forPlayer, $gamedata->id);
        $starttime = time();
        Firing::prepareFiring($servergamedata); //Marcin Sawicki, October 2017: new approach: calculate base hit chance first!
        $endtime = time();
	    
        $starttime = time();
        Firing::automateIntercept($servergamedata);
        $endtime = time();
	
        $starttime = time();
        Firing::fireWeapons($servergamedata);
        $endtime = time();
    //    Debug::log("RESOLVING FIRE - GAME: ".$gamedata->id." Time: " . ($endtime - $starttime) . " seconds.");
	
        Criticals::setCriticals($servergamedata);
	    
	self::$dbManager->submitFireorders($servergamedata->id, $servergamedata->getNewFireOrders(), $servergamedata->turn, 3);
        self::$dbManager->updateFireOrders($servergamedata->getUpdatedFireOrders());

        self::$dbManager->submitDamages($servergamedata->id, $servergamedata->turn, $servergamedata->getNewDamages());

        // check if adaptive Armour events did happen and submit
        $damagesAA = $servergamedata->getNewDamagesForAA();

        if ($damagesAA){
            foreach ($damagesAA as $entry){
            self::$dbManager->submitDamagesForAdaptiveArmour($servergamedata->id, $servergamedata->turn, $entry);
            }
        }

	// submit criticals
        self::$dbManager->submitCriticals($servergamedata->id,  $servergamedata->getUpdatedCriticals(), $servergamedata->turn);
    } //endof function startEndPhase


    
    private static function handleDeployment( $ships, $gamedata)
    {
        $moves = Deployment::validateDeployment($gamedata, $ships);
        foreach ($moves as $shipid=>$move)
        {
            self::$dbManager->insertMovement($gamedata->id, $shipid, $move);
        }
        
        self::$dbManager->updatePlayerStatus($gamedata->id, $gamedata->forPlayer, $gamedata->phase, $gamedata->turn);
        
    }
    
	
    private static function handleMovement( $ships, $gamedata ){        
        $turn = $gamedata->getActiveship()->getLastTurnMoved();
        if ($gamedata->turn <= $turn)
            throw new Exception("The ship has already moved");
        
        self::$dbManager->submitMovement($gamedata->id, $ships[$gamedata->activeship]->id, $gamedata->turn, $ships[$gamedata->activeship]->movement);
        
        $next = false;
        $nextshipid = -1;
        $firstship = null;
        foreach ($gamedata->ships as $ship){
            
            if ($firstship == null)
                $firstship = $ship;
                        
            if ($next && !$ship->isDestroyed() && !$ship->unavailable){
                $nextshipid = $ship->id;
                break;
            }
            
            if ($ship->id == $gamedata->activeship)
                $next = true;
        }
        
        if ($nextshipid > -1){
            $gamedata->setActiveship($nextshipid);
            self::$dbManager->updateGamedata($gamedata);
        }else{
            self::startWeaponAllocation($gamedata);
        }
	    
        return true;
    } //endof function handleMovement
	
    
    private static function changeTurn($gamedata){
    
        $gamedata->setTurn( $gamedata->turn+1 );
        if ($gamedata->turn === 1)
        {
            $gamedata->setPhase(-1); 
        }else{
            $gamedata->setPhase(1); 
        }
        
        $gamedata->setActiveship(-1);

        if (($gamedata->turn > 1 && $gamedata->isFinished()) || ($gamedata->status === "SURRENDERED")){
            $gamedata->status = "FINISHED";
        }
        else{
            $gamedata->status = "ACTIVE";
        }
            
        self::generateIniative($gamedata);
        self::$dbManager->updateGamedata($gamedata);

       // if ($gamedata->turn > 1){
         //   self::checkRegen($gamedata);
        //}   
               
        $servergamedata = self::$dbManager->getTacGamedata($gamedata->forPlayer, $gamedata->id);
        
        foreach ($servergamedata->ships as $key=>$ship){
            $movement = Movement::setPreturnMovementStatusForShip($ship, $servergamedata->turn);
            self::$dbManager->submitMovement($servergamedata->id, $ship->id, $servergamedata->turn, $movement, true);
        }
    }
    
    private static function generateIniative($gamedata){
        foreach ($gamedata->ships as $key=>$ship){
		/* moved to ship slass itself!
            $mod = 0;
            $speed = $ship->getSpeed();
        
            if ( !($ship instanceof OSAT) ){
           //     debug::log("speed check for: ".$ship->shipClass);
                if ($speed < 5){
                    $mod = (5-$speed)*10;
                }

                $CnC = $ship->getSystemByName("CnC");

                if ($CnC){
			    $mod += 5*($CnC->hasCritical("CommunicationsDisrupted", $gamedata->turn));
			    $mod += 10*($CnC->hasCritical("ReducedIniativeOneTurn", $gamedata->turn));
			    $mod += 10*($CnC->hasCritical("ReducedIniative", $gamedata->turn));
				//additional: SWTargetHeld (ship being held by Tractor Beam - reduces Initiative
	    			$mod += 20*($CnC->hasCritical("swtargetheld", $gamedata->turn)); //-4 Ini per hit
			}
	    }
	    */
	   $mod =  $ship->getCommonIniModifiers( $gamedata );
	   $iniBonus =  $ship->getInitiativebonus($gamedata);
	    

		
            $ship->iniative = Dice::d(100) + $iniBonus + $mod;
           //debug::log("ini submit for: ".$ship->shipClass."---:".$ship->iniative);

        }
        self::$dbManager->submitIniative($gamedata->id, $gamedata->turn, $gamedata->ships);
    }
    
    private static function getShipsFromJSON($json, $gameid){

        $ships = array();
        $array = json_decode($json, true);
        if (!is_array($array))
			return $ships;
			
        foreach ($array as $value) {
                    
            $movements = array();
            if (is_array($value["movement"])){
                foreach($value["movement"] as $i=>$move){
                    if(isset($move["at_initiative"])){
                        // For backwards compatibility
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
                                $move["value"],
                                $move["at_initiative"]
                        );
                    }
                    else{
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
                                $move["value"],
                                0
                        );
                    }
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
            
            
            $ship = new $value["phpclass"]($value["id"], $value["userid"], $value['name'], $value["slot"]);            
            $ship->setMovements($movements);    
            $ship->EW = $EW;

            if ($ship instanceof FighterFlight){
                $ship->flightSize = $value["flightSize"];  
                $ship->populate();
            }

            if ($ship instanceof WhiteStar){
                $ship->armourSettings = $value["armourSettings"];
            }

            
            foreach($value["systems"] as $i=>$system){
                //$sys = $ship->getSystemById($system['id']);
                $sys = $ship->getSystemById($i);

                
                if (isset($system["power"]) &&is_array($system["power"]))
                {
                    foreach ($system["power"] as $a=>$power)
                    {
                        $powerEntry = new PowerManagementEntry($power["id"], $power["shipid"], $power["systemid"], $power["type"], $power["turn"], $power["amount"]);
                        if (isset($sys)){
                            $sys->setPower($powerEntry);
                        }
                    }
                }
                
                if (isset($system["fireOrders"]) &&is_array($system["fireOrders"]))
                {
                    $fires = Array();
                    foreach($system["fireOrders"] as $i=>$fo){
                        $fireOrder = new FireOrder(-1, $fo["type"], $fo["shooterid"], $fo["targetid"], $fo["weaponid"], $fo["calledid"], $fo["turn"], $fo["firingMode"], 0, 0, $fo["shots"], 0, 0, $fo["x"], $fo["y"], $fo["damageclass"]);
                        if (isset($sys)){
                            $fires[] = $fireOrder;
                        }
                    }
                    
                    $sys->setFireOrders($fires);
                }
                
                if (isset($system["systems"]) && is_array($system["systems"]))
                {
                    foreach ($system["systems"] as $fightersys)
                    {
                        $fig = $sys->getSystemById($fightersys['id']);
                        if (isset($fightersys["fireOrders"]) && is_array($fightersys["fireOrders"]))
                        {
                            $fires = Array();
                            foreach($fightersys["fireOrders"] as $i=>$fo)
                            {
                                $fireOrder = new FireOrder(-1, $fo["type"], $fo["shooterid"], $fo["targetid"], $fo["weaponid"], $fo["calledid"], $fo["turn"], $fo["firingMode"], 0, 0, $fo["shots"], 0, 0, $fo["x"], $fo["y"], $fo["damageclass"]);
                                if (isset($fig)){
                                    $fires[] = $fireOrder;
                                }
                            }
                            
                            // plopje
                            if(isset($fightersys["ammo"])){
                                foreach($fightersys["ammo"] as $i=>$ammo){
                                    if(isset($ammo)){
                                        $fig->setAmmo($i, $ammo);
                                    }
                                }
                            }

                            $fig->setFireOrders($fires);
                        }                        
                    }
                    
                }
            }


            $ships[(int)$value["id"]] = $ship;
        }
        
        return $ships;
    }
    
}

?>
