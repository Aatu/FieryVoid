<?php
mysqli_report(MYSQLI_REPORT_ERROR);

class DBManager
{
    private $connection = null;
    private $testMode = false;
    private $id; // 👈 Add this line

    function __construct($host, $port, $database, $username, $password, $testMode = false)
    {
        $this->id = uniqid(); // This is now OK
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
		/*it turned out that empty ship name is plain problematic... force change it to SOMETHING!*/
		if($ship->name == ''){
				$ship->name = 'NAMELESS UNIT' ;
		}
		/*07.01.2024: merge options point cost into enhancements point cost! 
        $sql = "INSERT INTO `B5CGM`.`tac_ship` VALUES(null, $userid, $gameid, '" . $this->DBEscape($ship->name) . "', '" . $ship->phpclass . "', 0, 0, 0, 0, 0, $ship->slot, $ship->pointCostEnh)";
		*/
		$enhCostTotal = $ship->pointCostEnh + $ship->pointCostEnh2;
        $sql = "INSERT INTO `tac_ship` VALUES(null, $userid, $gameid, '" . $this->DBEscape($ship->name) . "', '" . $ship->phpclass . "', 0, 0, 0, 0, 0, $ship->slot, $enhCostTotal)";
		
        //   Debug::log($sql);
        $id = $this->insert($sql);
        return $id;
    }

	

	public function submitEnhancement($gameid, $shipid, $enhid, $numbertaken, $enhname){	
		try{
			$sql = "INSERT INTO `tac_enhancements` (gameid, shipid, enhid, numbertaken,enhname) 
				VALUES($gameid, $shipid, '$enhid', $numbertaken, '".$this->DBEscape($enhname)."' )";
			$this->insert($sql);
		}catch(Exception $e) {
			$this->endTransaction(true);
			throw $e;
		}
	} //endof function submitEnhancement
	

    public function submitFlightSize($gameid, $shipid, $flightSize)
    {
        $sql = "INSERT INTO `tac_flightsize` (gameid, shipid, flightsize)
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



    public function submitSavedList($name, $userid, $points, $isPublic) {
        // ✅ Force $isPublic into 0 or 1
        $isPublic = !empty($isPublic) ? 1 : 0;

        $sql = "INSERT INTO tac_saved_list (name, userid, points, isPublic) VALUES (?, ?, ?, ?)";
        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            throw new Exception("DB error in submitSavedList (prepare): " . $this->connection->error);
        }

        if (!$stmt->bind_param("siii", $name, $userid, $points, $isPublic)) {
            throw new Exception("DB error in submitSavedList (bind): " . $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("DB error in submitSavedList (execute): " . $stmt->error);
        }

        $newId = $this->connection->insert_id;

        $stmt->close();

        return $newId; // ✅ return the auto-generated ID
    }

    public function submitSavedShip($listId, $userid, $ship) 
    {
        // Ensure the ship has a valid name
        $shipName = $ship->name ?: 'NAMELESS UNIT';
        $flightsize = $ship->flightSize ?? 1;
		$enhCostTotal = $ship->pointCostEnh + $ship->pointCostEnh2;

        $sql = "INSERT INTO tac_saved_ship 
                (userid, listid, name, phpclass, flightsize, enhvalue)
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("DB prepare failed: " . $this->connection->error);
        }

        $stmt->bind_param(
            "iissii", 
            $userid,
            $listId,
            $shipName,
            $ship->phpclass,
            $flightsize,
            $enhCostTotal
        );

        if (!$stmt->execute()) {
            throw new Exception("DB execute failed: " . $stmt->error);
        }

        $id = $stmt->insert_id;
        $stmt->close();

        return $id;
    }

	public function submitSavedEnhancement($listId, $shipid, $enhid, $numbertaken, $enhname){	
		try{
			$sql = "INSERT INTO `tac_saved_enh` (listId, shipid, enhid, numbertaken,enhname) 
				VALUES($listId, $shipid, '$enhid', $numbertaken, '".$this->DBEscape($enhname)."' )";
			$this->insert($sql);
		}catch(Exception $e) {
			$this->endTransaction(true);
			throw $e;
		}
	} //endof function submitEnhancement

    public function submitSavedAmmo($listid, $shipid, $systemid, $firingMode, $ammoAmount)
    {
        $stmt = $this->connection->prepare("
            INSERT INTO tac_saved_ammo
                (listid, shipid, systemid, firingmode, ammo)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                ammo = VALUES(ammo)
        ");

        $stmt->bind_param(
            'iiiii',
            $listid, $shipid, $systemid, $firingMode, $ammoAmount
        );
        $stmt->execute();
        $stmt->close();
    }

    //All rows from tac_saved_list for a given userid
    public function getSavedFleets($userid) {
        $savedFleets = [];
        $stmt = $this->connection->prepare(
            "SELECT id, name, userid, points, isPublic
            FROM tac_saved_list
            WHERE userid = ? OR userid = 0
            ORDER BY userid DESC, name ASC" // optional: user fleets first
        );
        if ($stmt) {
            $stmt->bind_param('i', $userid);
            $stmt->execute();
            $stmt->bind_result($id, $name, $fleetUserId, $points, $isPublic); // renamed to avoid variable clash
            while ($stmt->fetch()) {
                $savedFleets[] = [
                    'id' => $id,
                    'name' => $name,
                    'userid' => $fleetUserId,
                    'points' => $points,
                    'isPublic' => $isPublic
                ];
            }
            $stmt->close();
        }
        return $savedFleets;
    }

    //Just one row from tac_saved_list
    public function getSavedFleet($id) {
        $savedFleet = null;

        $stmt = $this->connection->prepare(
            "SELECT id, name, userid, points, isPublic
            FROM tac_saved_list
            WHERE id = ?"
        );

        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($id, $name, $userid, $points, $isPublic);

            if ($stmt->fetch()) {
                $savedFleet = [
                    'id' => $id,
                    'name' => $name,
                    'userid' => $userid,
                    'points' => $points,
                    'isPublic' => (bool) $isPublic
                ];
            }

            $stmt->close();
        }

        return $savedFleet;
    }

    public function getSavedShips($listid)
    {
        $ships = array();

        $stmt = $this->connection->prepare(
            "SELECT
                id, userid, name, phpclass, flightsize, enhvalue
            FROM
                tac_saved_ship 
            WHERE
                listid = ?
            "
        );

        if ($stmt) {
            $stmt->bind_param('i', $listid);
            $stmt->bind_result($shipid, $userid, $name, $phpclass, $flightsize, $enhvalue);
            $stmt->execute();
            while ($stmt->fetch()) {
                $ship = new $phpclass($shipid, $userid, $name, 1);
                if($ship instanceof FighterFlight) $ship->flightSize = $flightsize;
				$ship->pointCostEnh = $enhvalue;
                $ships[] = $ship;
            }
            $stmt->close();
        }

        return $ships;
    }


    public function getSavedEnhancementsForShip($shipid){
        $Enhancements = array();
        $stmt = $this->connection->prepare( //enhname will be used for info tooltip!
                "SELECT 
                    enhid, numbertaken, enhname
                FROM 
                    tac_saved_enh 
                WHERE 
                    shipid = ?
                "
            );
            if ($stmt)
            {
                $stmt->bind_param('i', $shipid);
                $stmt->bind_result($enhID, $numbertaken, $description);
                $stmt->execute();
                while ($stmt->fetch())
                {
                $Enhancements[] = array($enhID,$numbertaken,$description);
                }
                $stmt->close();                
            }
        return $Enhancements;
    }

       public function getSavedAmmoForShip($shipid){
        $ammoEntry = array();
        $stmt = $this->connection->prepare( //enhname will be used for info tooltip!
                "SELECT 
                    systemid, firingmode, ammo
                FROM 
                    tac_saved_ammo 
                WHERE 
                    shipid = ?
                "
            );
            if ($stmt)
            {
                $stmt->bind_param('i', $shipid);
                $stmt->bind_result($systemid, $firingmode, $ammo);
                $stmt->execute();
                while ($stmt->fetch())
                {
                    $ammoEntry[] = array($systemid,$firingmode,$ammo);
                }
                $stmt->close();                
            }
        return $ammoEntry;
    }

    public function changeAvailabilityFleet(int $id): int {
        try {
            // Toggle the value
            $stmt = $this->connection->prepare(
                "UPDATE tac_saved_list
                SET isPublic = CASE WHEN isPublic = 1 THEN 0 ELSE 1 END
                WHERE id = ?"
            );
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->close();

            // Fetch the new value
            $stmt = $this->connection->prepare(
                "SELECT isPublic FROM tac_saved_list WHERE id = ?"
            );
            $stmt->bind_param('i', $id);
            $stmt->execute();

            $newValue = 0;
            $stmt->bind_result($newValue);
            $stmt->fetch();
            $stmt->close();

            return (int) $newValue;

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function deleteSavedFleet($id) {
        try{
            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_saved_list
                WHERE
                    id = ?"
            );
            if ($stmt) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            throw $e;
        }
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
            // Identify slots to reset (for Ladder) BEFORE they are cleared
            $slotsToReset = [];
            if ($slotid) {
                $slotsToReset[] = $slotid;
            } else {
                 $findSql = "SELECT slot FROM tac_playeringame WHERE gameid = $gameid AND playerid = $userid";
                 $findRes = $this->query($findSql);
                 foreach ($findRes as $row) {
                     $slotsToReset[] = $row->slot;
                 }
            }

            $sql = "DELETE FROM `tac_ship` WHERE tacgameid = $gameid AND playerid = $userid";
            if ($slotid)
                $sql .= " AND slot = $slotid";

            $this->update($sql);

            $sql = "UPDATE tac_playeringame SET playerid = null, lastphase = -3, lastturn = 0 WHERE gameid = $gameid AND playerid = $userid";
            if ($slotid)
                $sql .= " AND slot = $slotid";

            $this->update($sql);
            
            // Ladder Reset Logic
            if (count($slotsToReset) > 0) {
                $gSql = "SELECT rules, creator FROM tac_game WHERE id = $gameid";
                $gRes = $this->query($gSql);
                if ($gRes && count($gRes) > 0) {
                    $rules = json_decode($gRes[0]->rules, true);
                    if (isset($rules['ladder']) && $rules['ladder']) {
                         $creatorId = $gRes[0]->creator;
                         $basePoints = 0;
                         
                         // Try to get creator's points
                         if ($creatorId) {
                             $cSql = "SELECT points FROM tac_playeringame WHERE gameid = $gameid AND playerid = $creatorId";
                             $cRes = $this->query($cSql);
                             if ($cRes && count($cRes) > 0) {
                                 $basePoints = $cRes[0]->points;
                             }
                         }
                         
                         // Fallback to min points in game
                         if ($basePoints == 0) {
                             $sSql = "SELECT points FROM tac_playeringame WHERE gameid = $gameid";
                             $sRes = $this->query($sSql);
                             $minPoints = 99999999;
                             foreach($sRes as $row){
                                 if ($row->points < $minPoints && $row->points > 0) $minPoints = $row->points;
                             }
                             if ($minPoints < 99999999) $basePoints = $minPoints;
                         }
                         
                         if ($basePoints > 0) {
                             foreach ($slotsToReset as $rSlot) {
                                 $updSql = "UPDATE tac_playeringame SET points = $basePoints WHERE gameid = $gameid AND slot = $rSlot";
                                 $this->update($updSql);
                             }
                         }
                    }
                }
            }

        } catch (Exception $e) {
            throw $e;
        }
    }

    public function shouldBeInGameLobby($userid)
    {
        try {
            $sql = "SELECT * FROM `tac_game` g join `tac_playeringame` p on g.id = p.gameid where p.playerid = $userid and g.status = 'LOBBY';";

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
            $sql = "SELECT * FROM `tac_playeringame` WHERE gameid = $gameid AND teamid != " . $slot->team . " AND playerid = $userid";
            if ($this->found($sql)) {
                $this->leaveSlot($userid, $gameid);
            }

            $sql = "UPDATE tac_playeringame SET playerid = $userid WHERE gameid = $gameid and slot = $slotid";
            $this->update($sql);
            
            // Ladder Handicap Logic
            $gSql = "SELECT rules FROM tac_game WHERE id = $gameid";
            $gRes = $this->query($gSql);
            
            if ($gRes && count($gRes) > 0) {
                $rules = json_decode($gRes[0]->rules, true);
                if (isset($rules['ladder']) && $rules['ladder']) {
                    //error_log("Ladder Logic Triggered for Game $gameid User $userid");
                    
                    $slots = $this->getSlotsInGame($gameid);
                    $mySlot = null;
                    $oppSlot = null;
                    
                    foreach ($slots as $s) {
                        if ($s->slot == $slotid) $mySlot = $s;
                        else if ($s->playerid != null) $oppSlot = $s; 
                    }
                    
                    if ($mySlot && $oppSlot) {
                        $myRating = 100;
                        $oppRating = 100;
                        
                        $rSql = "SELECT rating FROM tac_ladder_rankings WHERE playerid = " . $userid;
                        $rRes = $this->query($rSql);
                        if ($rRes && count($rRes) > 0) $myRating = $rRes[0]->rating;
                        
                        $rSql2 = "SELECT rating FROM tac_ladder_rankings WHERE playerid = " . $oppSlot->playerid;
                        $rRes2 = $this->query($rSql2);
                        if ($rRes2 && count($rRes2) > 0) $oppRating = $rRes2[0]->rating;
                        
                        //error_log("Ratings - Me: $myRating (Slot $slotid), Opp: $oppRating (Player " . $oppSlot->playerid . ")");
                        
                        $diff = abs($myRating - $oppRating);
                        
                        // Use Opponent's points as Base (Do NOT modify opponent)
                        $basePoints = $oppSlot->points;
                        $bonusPoints = round($basePoints * ($diff / 100));
                        
                        //error_log("Base: $basePoints, Diff: $diff, Bonus: $bonusPoints");

                        if ($myRating < $oppRating) {
                            // Joiner is Weaker: Joiner gets BONUS (+)
                             //error_log("Applying BONUS: " . ($basePoints + $bonusPoints));
                             $this->update("UPDATE tac_playeringame SET points = " . ($basePoints + $bonusPoints) . " WHERE gameid = $gameid AND slot = $slotid");
                        } else if ($myRating > $oppRating) {
                             // Joiner is Stronger: Joiner gets PENALTY (-)
                             // OLD WAY: $points = $basePoints - $bonusPoints; // effective: Base * (1 - diff/100)
                             // NEW WAY: Base / (1 + diff/100)
                             
                             $factor = 1 + ($diff / 100);
                             $newPoints = round($basePoints / $factor);
                             
                             //error_log("Applying PENALTY: " . $newPoints);
                             $this->update("UPDATE tac_playeringame SET points = " . $newPoints . " WHERE gameid = $gameid AND slot = $slotid");
                        } else {
                             // Equal ratings: Joiner gets Base
                             //error_log("Applying BASE: " . $basePoints);
                             $this->update("UPDATE tac_playeringame SET points = " . $basePoints . " WHERE gameid = $gameid AND slot = $slotid");
                        }
                    } else {
                        //error_log("Ladder Logic: slots not found? MySlot: " . ($mySlot ? "Yes" : "No") . ", OppSlot: " . ($oppSlot ? "Yes" : "No"));
                    }
                }
            }

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
            (
                gameid,
                slot,
                playerid,
                teamid,
                lastturn,
                lastphase,
                lastactivity,
                submitLock,
                name,
                points,
                depx,
                depy,
                deptype,
                depwidth,
                depheight,
                depavailable,
                waiting,
                surrendered
            )
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
                false,
                ?
            )
        ");

        if ($stmt) {
            foreach ($slots as $slot) {
                $stmt->bind_param(
                    'iiiiiisiiisiiii', // ✅ added an extra 'i' for status at the end
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
                    $slot->depavailable,
                    $slot->surrendered // ✅ NEW
                );
                $stmt->execute();
            }
            $stmt->close();
        }
    }

/* //Old version
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
*/        


    public function createGame($gamename, $background, $slots, $userid, $gamespace, $description, $rules = '{}')
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
                ?,
		?
            )
        ");

        if ($stmt) {
            //$gamename = $this->DBEscape($gamename);
            $background = $this->DBEscape($background);
            $slotnum = count($slots);
            $gamespace = $this->DBEscape($gamespace);
            $stmt->bind_param(
                'ssiisss',
                $gamename,
                $background,
                $slotnum,
                $userid,
                $gamespace,
                $rules,
		$description
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
				if($critical->id < 1) $critical->forceModify = false;//cannot modify a critical that's not in the database yet!
                //if ((!$critical->newCrit) && (!$critical->forceModify) && ($critical->turn != $turn)  ) continue; //replaced by conditions below
				if ($critical->forceModify){ //modification of critical that already exists in database - modifying turn end! (the only thing modifiable)
					$turnend = $critical->turnend;
					$critid = $critical->id;
					$sql = "UPDATE `tac_critical` SET turnend = " . $turnend . " where id = " . $critid . "";
				} else if ( $critical->id < 1 ){ //actual new critical
					//important to use $critical->turn: critical does NOT need to have turn equal to current! 
					//this is importnat for criticals that need to have limited time window yet last longer than 1 turn (go out 1 turn after issuing - so issue must be later)
					$sql = "INSERT INTO `tac_critical` VALUES(null, $gameid, " . $critical->shipid . ", " . $critical->systemid . ",'" . $critical->phpclass . "'," . $critical->turn . ", " . $critical->turnend . ",'" . $critical->param . "')";
				} else continue;
                $this->update($sql);
            }
			
			/*previous version:
			foreach ($criticals as $critical) {
                if ((!$critical->newCrit) && ($critical->turn != $turn))
                    continue;
				//important to use $critical->turn: critical does NOT need to have turn equal to current! 
				//this is importnat for criticals that need to have limited time window yet last longer than 1 turn (go out 1 turn after issuing - so issue must be later)
                $sql = "INSERT INTO `B5CGM`.`tac_critical` VALUES(null, $gameid, " . $critical->shipid . ", " . $critical->systemid . ",'" . $critical->phpclass . "'," . $critical->turn . ",'" . $critical->param . "')";

                $this->update($sql);
            }
			*/


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
	        targetid = ?,
	    	firingmode = ?,
                needed = ?,
                rolled = ?,
                notes = ?,
                pubnotes = ?,
                shots = ?,
                shotshit = ?,
                intercepted = ?,
                x = ?,
                y = ?,
		resolutionorder = ?
            WHERE
                id = ?
            "
        );

        if ($stmt) {
            foreach ($fireOrders as $fire) {
                $stmt->bind_param(
                    'iiiissiiiiiii',
                    $fire->targetid,
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
		    $fire->resolutionOrder,
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

  		if (($fire->type == "ballistic") && ($phase != 1) &&  ($fire->addToDB != true)) //28 Sept 2023 - Amended to enable Multimissile to shows multiple shots in Combat Log.
                continue;

            if ($fire->type != "ballistic" && $phase == 1)
                continue;

  		    if (($fire->type == "prefiring") && ($phase != 5))
                continue;

            if ($fire->type != "prefiring" && $phase == 5)
                continue;            

            $sql = "INSERT INTO `tac_fireorder` VALUES (null, '" . $fire->type . "', " . $fire->shooterid . ", " . $fire->targetid . ", " . $fire->weaponid . ", " . $fire->calledid . ", " . $fire->turn . ", "
                . $fire->firingMode . ", " . $fire->needed . ", " . $fire->rolled . ", $gameid, '" . $fire->notes . "', " . $fire->shotshit . ", " . $fire->shots . ", '" . $fire->pubnotes . "', 0, '" . $fire->x . "', '" . $fire->y . "', '" . $fire->damageclass . "', '" . $fire->resolutionOrder . "')";

            $this->update($sql);
        }
    }

/* //OLD VERSION - Oct 2025
    public function submitPower($gameid, $turn, $powers)
    {

        try {


            foreach ($powers as $power) {
                if ($power->turn != $turn)
                    continue;

                //$id, $shipid, $systemid, $type, $turn, $amount
                $sql = "INSERT INTO `tac_power` VALUES( null, " . $power->shipid . ", " . $gameid . ", " . $power->systemid . ", " . $power->type . ", " . $turn .
                    ", " . $power->amount . ")";

                $this->update($sql);
            }


        } catch (Exception $e) {
            throw $e;
        }

    }
*/

    //New version the normalises and prevents duplication to accommodate things like Fighter systems being boosted
    public function submitPower($gameid, $turn, $powers)
    {
        try {
            // --- SAFETY NORMALIZATION LAYER ---
            $normalized = [];

            foreach ($powers as $p) {
                if (is_object($p)) {
                    $normalized[] = $p;
                } elseif (is_array($p)) {
                    // Handle nested single-element array: [[PowerEntry]]
                    if (count($p) === 1 && is_object(reset($p))) {
                        $normalized[] = reset($p);
                    } else {
                        // Convert associative array to object
                        $obj = (object)$p;
                        //error_log("[submitPower] Warning: Power entry was array, normalized. Contents: " . json_encode($p));
                        $normalized[] = $obj;
                    }
                } else {
                    // Unexpected type — log it and skip
                    error_log("[submitPower] Warning: Invalid power entry type (" . gettype($p) . ")");
                }
            }

            $powers = $normalized;
            // --- END NORMALIZATION LAYER ---

            // --- DEDUPLICATION LAYER ---
            $seen = [];

            foreach ($powers as $power) {
                if (!is_object($power)) continue;

                if (!property_exists($power, 'turn') || $power->turn != $turn) continue;

                // Create a unique key per power entry
                $key = $power->shipid . '-' . $power->systemid . '-' . $power->type . '-' . $turn;

                if (isset($seen[$key])) {
                    error_log("[submitPower] Skipping duplicate power entry for key: $key");
                    continue;
                }
                $seen[$key] = true;
                // --- END DEDUPLICATION ---

                $sql = "INSERT INTO `tac_power` VALUES(
                    null,
                    " . (int)$power->shipid . ",
                    " . (int)$gameid . ",
                    " . (int)$power->systemid . ",
                    " . (int)$power->type . ",
                    " . (int)$turn . ",
                    " . (int)$power->amount . "
                )";

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
                $undes = ($damage->undestroyed) ? 1 : 0;
                $fireID = $damage->fireorderid;

                if ($fireID < 0){ //Marcin Sawicki: fire order ID not known at the moment of dealing damage!
                    //read it from database by source, target and weapon ID (if multiple ones fit - assign to any of them _that hit_
                    try{
                        $targetid = $damage->shipid;
                        $shooterid = $damage->shooterid; //additional field
                        $weaponid = $damage->weaponid; //additional field
                        //targetid = -1 if weapon is hex targeted!
                        $sql1 = "SELECT * FROM `tac_fireorder` where gameid = $gameid and turn = $turn and shooterid = $shooterid and (targetid = $targetid or targetid = -1) and weaponid = $weaponid and shotshit >0";
                        $result = $this->query($sql1);
                        if ($result == null || sizeof($result) == 0){  //nothing, keep -1 as ID
                        }else{
                            $fireID = $result[0]->id;
                        }
                    }catch(Exception $e) { //nothing, keep -1 as ID
                    }
                }

                //$id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields;
                $sql = "INSERT INTO `tac_damage` VALUES( null, ".$damage->shipid.", ".$gameid.", ".$damage->systemid.", ".$turn.", ".$damage->damage.
                    ", ".$damage->armour. ", ".$damage->shields.", ".$fireID .", ".$des.", ".$undes.", '".$damage->pubnotes."', '".$damage->damageclass."')";


                $this->update($sql);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }



    public function submitIniative($gameid, $turn, $ships)
    {

        try {

            foreach ($ships as $ship) {
                //$unmodified = $ship->unmodifiedIniative === null ? 'NULL' : $ship->unmodifiedIniative;
				//I THINK unmodified ini should mean INI bonus...
				$unmodified = $ship->unmodifiedIniative === null ? $ship->iniativebonus : $ship->unmodifiedIniative;				
                $sql = "INSERT INTO `tac_iniative` VALUES($gameid, " . $ship->id . ", $turn, " . $ship->iniative . ", " . $unmodified .")";
                $this->update($sql);
            }
        } catch (Exception $e) {
            throw $e;
        }

    }

    public function updatePlayerStatus($gameid, $userid, $phase, $turn, $slots = null)
    {
        try {
            $sql = "UPDATE `tac_playeringame` SET `lastturn` = $turn, `lastphase` = $phase, `lastactivity` = NOW() WHERE"
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

    //Used to skip Slot forward through phases if it has no ships deployed. Updates specific slot, not just ALL player's slots as updatePlayerStatus() does.
    public function updatePlayerSlotPhase($gameid, $userid, $slot, $phase, $turn)
    {
        try { 

            $sql = "UPDATE `tac_playeringame`
                    SET `lastturn` = $turn,
                        `lastphase` = $phase,
                        `lastactivity` = NOW()
                    WHERE gameid = $gameid
                    AND playerid = $userid
                    AND slot = $slot";

            $this->update($sql);
        } catch (Exception $e) {
            throw $e;
        }
    }
/*
        //Update depavailable at start of game.
        public function updatePlayerStatusDeploy($gameid, $userid, $slot, $phase, $turn, $minDeploy)
    {
        try { 
            $sql = "UPDATE `tac_playeringame`
                    SET `lastturn` = $turn,
                        `lastphase` = $phase,
                        `depavailable` = $minDeploy,
                        `lastactivity` = NOW()
                    WHERE gameid = $gameid
                    AND playerid = $userid
                    AND slot = $slot";

            $this->update($sql);
        } catch (Exception $e) {
            throw $e;
        }
    }
*/    
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

                $sql = "INSERT INTO `tac_ew` VALUES (null, $gameid, " . $entry->shipid . ", $turn, '" . $entry->type . "', " . $entry->amount . ", " . $entry->targetid . ")";

                $this->insert($sql);
            }

        } catch (Exception $e) {

            throw $e;
        }
    }

/* no longer needed, Adaptive Armor redone
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
*/

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
                        'iisiiiiiiiissisi', //21.05.2021 - combat pivot apparently not registered correctly; original: 'iisiiiiiiiissiii'
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


	/*acepts IndividualNote OBJECT
		inserts it if it doesn't yet have ID, does NOT update old ones
	*/
    public function insertIndividualNote($noteObject)
    {
		if ($noteObject->id > -1) return; //old note, do not insert

        try {
            $stmt = $this->connection->prepare(
                "INSERT INTO  
                    tac_individual_notes
                VALUES 
                ( 
                    null,?,?,?,?,?,?,?,?
                )"
            );

            if ($stmt) {
				$stmt->bind_param(
					'iiiiisss',
					$noteObject->gameid,
					$noteObject->turn,
					$noteObject->phase,
					$noteObject->shipid,
					$noteObject->systemid,
					$noteObject->notekey,
					$noteObject->notekey_human,
					$noteObject->notevalue
				);
				$stmt->execute();
			}
			$stmt->close();
        } catch (Exception $e) {
            throw $e;
		}
    }//endof function insertIndividualNote
	
	
	//checks if a given ship was already moved this turn
	public function isMovementAlreadySubmitted($gameid, $shipid, $turn)
    {
		$sql = "SELECT * FROM tac_shipmovement 
			WHERE gameid = $gameid and turn = $turn and shipid = $shipid and preturn <> 1 and type <> 'deploy'"; 
        $result = $this->query($sql);
        if ($result == null || sizeof($result) == 0){ //no movement entries other than pre-turn and deployment
			return false;
		}else{ //movement for indicated ship/turn is already present!
			return true;
		}
    } //endof function isMovementAlreadySubmitted
	

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
		WHERE turn > 0 and DATE_ADD(p.lastactivity, INTERVAL 2 day) >= NOW() "; //skip games in creation phase
        //    debug::log($sql);

        $result = $this->query($sql);

        if ($result == null || sizeof($result) == 0)
            return null;

        foreach ($result as $value) {
            $game = new TacGamedata($value->id, $value->turn, $value->phase, json_decode($value->activeship), $playerid, $value->name, $value->status, $value->points, $value->background, $value->creator, $value->description, $value->gamespace);
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
        //$stmt = $this->connection->prepare("select g.id, g.name, pg.waiting from tac_playeringame pg join tac_game g on pg.gameid = g.id where g.status = 'ACTIVE' AND pg.playerid = ?");
		//enhance to include game rules:
        $stmt = $this->connection->prepare("
            SELECT g.id, g.name, pg.waiting, g.gamespace, g.rules
            FROM tac_playeringame pg
            JOIN tac_game g ON pg.gameid = g.id
            WHERE g.status = 'ACTIVE'
            AND pg.playerid = ?
            AND pg.surrendered IS NULL
        ");  	    

        $games = [];
		
		$nm = '';
        if ($stmt) {
            $stmt->bind_param('i', $playerid);
            $stmt->bind_result($id, $gameName, $waiting, $gamespace, $rules);
            $stmt->execute();
			while ($stmt->fetch()) {
				$nm = $gameName;

				if (strpos($rules, 'ladder')!==false){
				    $nm = '<span style="font-weight:bold; color:gold; padding-right: 0px;">LADDER: </span>' . $gameName;
                } else {                                    
				    $nm = $gameName;
                }                
                /*$nm .= ' <br><span class="gameRules">(';
			    //gamespace and rules: add to name!    
				if ($gamespace == '-1x-1'){ //open map
					$nm .= 'Open';
				}else{ //fixed map
					$nm .= $gamespace;
				}
				if (strpos($rules, 'initiativeCategories')!==false){//simultaneous movement
					$nm  .= ', Sim Mov';
				}else{//standard movement
					$nm  .= ', Std Mov';
				}
                if (strpos($rules, 'moons') !== false || strpos($rules, 'asteroids') !== false) {
                    $nm .= ', Terrain';
                }             
				if (strpos($rules, 'desperate')!==false){
					$nm  .= ', Desparate';
				}

                $nm .= ')</span>';
                */
				
				//attempt to fix "no highlight" bug - do highlight a game if no player is listed as active
                $games[] = ["id" => $id, "name" => $nm, "waiting" => $waiting, "status" => "ACTIVE"];
            }
            $stmt->close();
        }
		
		//attempt to solve no highlight problem - do highlight the game if no player is listed as active
				
		foreach($games as $currLineId=>$currGameData) if($games[$currLineId]["waiting"] != 0){
			//$games[$currLineId]["waiting"] = 0;
			$currGameId = $games[$currLineId]["id"];
			$sql = "SELECT DISTINCT slot FROM tac_playeringame WHERE gameid = $currGameId and waiting = 0 "; //are there players that are waiting for action?
			$result = $this->query($sql);
			if (($result == null) || (sizeof($result) == 0)){ //no such players do exist
				$games[$currLineId]["waiting"] = 0;				
			}
		}
        return $games;
    }

    /*
    public function getPlayerGames($playerid) {
        //$stmt = $this->connection->prepare("select g.id, g.name, pg.waiting from tac_playeringame pg join tac_game g on pg.gameid = g.id where g.status = 'ACTIVE' AND pg.playerid = ?");
		//enhance to include game rules:
		$stmt = $this->connection->prepare("select g.id, g.name, pg.waiting, g.gamespace, g.rules from tac_playeringame pg join tac_game g on pg.gameid = g.id where g.status = 'ACTIVE' AND pg.playerid = ?");
		    	    
        $games = [];
		
		$nm = '';
        if ($stmt) {
            $stmt->bind_param('i', $playerid);
            $stmt->bind_result($id, $gameName, $waiting, $gamespace, $rules);
            $stmt->execute();
			while ($stmt->fetch()) {
				$nm = $gameName;
				$nm .= ' (';
			//gamespace and rules: add to name!    
				if ($gamespace == '-1x-1'){ //open map
					$nm .= 'open';
				}else{ //fixed map
					$nm .= $gamespace;
				}
				if (strpos($rules, 'initiativeCategories')!=false){//simultaneous movement
					$nm  .= ', sim mv';
				}else{//standard movement
					$nm  .= ', std mv';
				}		    
				$nm  .= ')';
				
				//attempt to fix "no highlight" bug - do highlight a agame if no player is listed as active
                $games[] = ["id" => $id, "name" => $nm, "waiting" => $waiting, "status" => "ACTIVE"];
            }
            $stmt->close();
        }
		
		//attempt to solve no highlight problem - do highlight the game if no player is listed as active
				
		foreach($games as $currLineId=>$currGameData) if($games[$currLineId]["waiting"] != 0){
			//$games[$currLineId]["waiting"] = 0;
			$currGameId = $games[$currLineId]["id"];
			$sql = "SELECT DISTINCT slot FROM tac_playeringame WHERE gameid = $currGameId and waiting = 0 "; //are three players that are waiting for action?
			$result = $this->query($sql);
			if (($result == null) || (sizeof($result) == 0)){ //no such players do exist
				$games[$currLineId]["waiting"] = 0;				
			}
		}
        return $games;
    }
	*/
    public function getPlayerName($playerid) {
		$playerName = '';	
		        
		$sql = "SELECT DISTINCT username FROM player WHERE id = $playerid "; 
        $result = $this->query($sql);
        if ($result == null || sizeof($result) == 0) return '';
        foreach ($result as $value) {
			$playerName = $value->username;
        }
        return $playerName;
    }

    public function getLobbyGames($userid) {
        //$stmt = $this->connection->prepare("select g.id as parentGameId, g.name, g.slots, (select count(gameid) from tac_playeringame where gameid = parentGameId ) as numberOfPlayers from tac_game g WHERE  g.status = 'LOBBY';");
		//above always returns playerCount = number of slots, let's try different approach (Marcin Sawicki):
		//$stmt = $this->connection->prepare("select g.id as parentGameId, g.name, g.slots, (select count(distinct playerid) from tac_playeringame where gameid = parentGameId and playerid > 0 ) as numberOfPlayers from tac_game g WHERE  g.status = 'LOBBY';");    
		//enhance to include game rules
		$stmt = $this->connection->prepare("select g.id as parentGameId, g.name, g.slots, g.gamespace, g.rules, (select count(distinct playerid) from tac_playeringame where gameid = parentGameId and playerid > 0 ) as numberOfPlayers, (SELECT count(*) FROM tac_playeringame WHERE gameid = g.id AND playerid = ?) as userInGame from tac_game g WHERE  g.status = 'LOBBY';");    
		
        $games = [];
		$nm = '';

        if ($stmt) {
            $stmt->bind_param("i", $userid);
            $stmt->bind_result($id, $gameName, $slots, $gamespace, $rules, $playerCount, $userInGame);
			//$stmt->bind_result($id, $gameName, $slots, $playerCount );
            $stmt->execute();
            while ($stmt->fetch()) {
                // FILTER: If it's a Fleet Test game, and user is not in it, SKIP.
                $rulesObj = json_decode($rules, true);
                if (isset($rulesObj['fleetTest']) && $rulesObj['fleetTest'] == 1 && $userInGame == 0) {
                    continue;
                }

				if (strpos($rules, 'ladder')!==false){
				    $nm = '<span style="font-weight:bold; color:#52b352; padding-right: 0px;">LADDER: </span>' . $gameName;
                } else {                                    
				    $nm = $gameName;
                }
                $nm .= ' <br><span class="gameRules">(';
			    //gamespace and rules: add to name!
				if ($gamespace == '-1x-1'){ //open map
					$nm .= 'Open';
				}else{ //fixed map
					$nm .= $gamespace;
				}

				if (strpos($rules, 'ladder')!==false){
					$nm  .= ', Ranked';
				}     

				if (strpos($rules, 'initiativeCategories')!==false){//simultaneous movement
					$nm  .= ', Sim. Mov';
				}else{//standard movement
					$nm  .= ', Std Mov';
				}
                if (strpos($rules, 'moons') !== false || strpos($rules, 'asteroids') !== false) {
                    $nm .= ', Terrain';
                }             
				if (strpos($rules, 'desperate')!==false){
					$nm  .= ', Desperate';
				}
				if (strpos($rules, 'friendlyFire')!==false){
					$nm  .= ', Friendly Fire';
				}                 

                $nm .= ')</span>';

                $fleetTest = false;
                $ladder = false;
                if (strpos($rules, 'ladder')!==false){
                    $ladder = true;
                }

                //To mark Fleet Test games as Fleet Test in lobby
                if (isset($rulesObj['fleetTest']) && $rulesObj['fleetTest'] == 1) {
                    $nm = '<span style="color:gold; font-weight:bold;">Fleet Test</span>'; 
                    $fleetTest = true;                    
                }    

                $games[] = ["id" => $id, "name" => $nm, "slots" => $slots, "playerCount" => $playerCount, "status" => "LOBBY", "test" => $fleetTest, "ladder" => $ladder];
            }
            $stmt->close();
        }
        return $games;

    }
    
    /*
    public function getLobbyGames() {
        //$stmt = $this->connection->prepare("select g.id as parentGameId, g.name, g.slots, (select count(gameid) from tac_playeringame where gameid = parentGameId ) as numberOfPlayers from tac_game g WHERE  g.status = 'LOBBY';");
		//above always returns playerCount = number of slots, let's try different approach (Marcin Sawicki):
		//$stmt = $this->connection->prepare("select g.id as parentGameId, g.name, g.slots, (select count(distinct playerid) from tac_playeringame where gameid = parentGameId and playerid > 0 ) as numberOfPlayers from tac_game g WHERE  g.status = 'LOBBY';");    
		//enhance to include game rules
		$stmt = $this->connection->prepare("select g.id as parentGameId, g.name, g.slots, g.gamespace, g.rules, (select count(distinct playerid) from tac_playeringame where gameid = parentGameId and playerid > 0 ) as numberOfPlayers from tac_game g WHERE  g.status = 'LOBBY';");    
		
        $games = [];
		$nm = '';

        if ($stmt) {
            $stmt->bind_result($id, $gameName, $slots, $gamespace, $rules, $playerCount );
			//$stmt->bind_result($id, $gameName, $slots, $playerCount );
            $stmt->execute();
            while ($stmt->fetch()) {
				$nm = $gameName;
				$nm .= ' (';
			//gamespace and rules: add to name!    
				if ($gamespace == '-1x-1'){ //open map
					$nm .= 'open';
				}else{ //fixed map
					$nm .= $gamespace;
				}
				if (strpos($rules, 'initiativeCategories')!=false){//simultaneous movement
					$nm  .= ', sim mv';
				}else{//standard movement
					$nm  .= ', std mv';
				}		    
				$nm  .= ')';
                $games[] = ["id" => $id, "name" => $nm, "slots" => $slots, "playerCount" => $playerCount, "status" => "LOBBY"];
            }
            $stmt->close();
        }
        return $games;

    }
    */        
    public function getTacGame($gameid, $playerid)
    {
         $sql = "SELECT * FROM `tac_game` where id = $gameid";
    

        $games = array();

        try {
            $result = $this->query($sql);

            if ($result == null || sizeof($result) == 0)
                return null;

            foreach ($result as $value) {
                $game = new TacGamedata($value->id, $value->turn, $value->phase, json_decode($value->activeship), $playerid, $value->name, $value->status, $value->points, $value->background, $value->creator, $value->description, $value->gamespace, json_decode($value->rules, true));
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
/* //OLD VERSION WITHOUT WAITING/STATUS VARIABLES - DK June 2025
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
*/


    public function getSlotsInGame($gameid)
    {
        $slots = array();

        $stmt = $this->connection->prepare("
            SELECT 
                playerid, slot, teamid, lastturn, lastphase, name, points,
                depx, depy, deptype, depwidth, depheight, depavailable,
                p.username, waiting, surrendered
            FROM 
                tac_playeringame pg
            LEFT JOIN 
                player p ON p.id = pg.playerid
            WHERE 
                gameid = ?
        ");

        if ($stmt) {
            $stmt->bind_param('i', $gameid);
            $stmt->bind_result(
                $playerid, $slot, $teamid, $lastturn, $lastphase, $name, $points,
                $depx, $depy, $deptype, $depwidth, $depheight, $depavailable,
                $username, $waiting, $surrendered // ✅ added surrendered
            );
            $stmt->execute();
            while ($stmt->fetch()) {
                $slots[$slot] = new PlayerSlot(
                    $playerid, $slot, $teamid, $lastturn, $lastphase, $name, $points,
                    $depx, $depy, $deptype, $depwidth, $depheight, $depavailable,
                    $username, $waiting, $surrendered // ✅ pass surrendered into PlayerSlot
                );
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
                playerid, slot, teamid, lastturn, lastphase, name, points,
                depx, depy, deptype, depwidth, depheight, depavailable,
                p.username, waiting, surrendered
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
            $stmt->bind_result(
                $playerid, $slot, $teamid, $lastturn, $lastphase, $name, $points,
                $depx, $depy, $deptype, $depwidth, $depheight, $depavailable,
                $username, $waiting, $surrendered // ✅ added surrendered
            );
            $stmt->execute();
            while ($stmt->fetch()) {
                $slot = new PlayerSlot(
                    $playerid, $slot, $teamid, $lastturn, $lastphase, $name, $points,
                    $depx, $depy, $deptype, $depwidth, $depheight, $depavailable,
                    $username, $waiting, $surrendered // ✅ pass surrendered
                );
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
                id, playerid, name, phpclass, slot, enhvalue
            FROM
                tac_ship 
            WHERE
                id = ?
            "
        );

        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->bind_result($id, $playerid, $name, $phpclass, $slot, $enhvalue);
            $stmt->execute();
            while ($stmt->fetch()) {
                $ship = new $phpclass($id, $playerid, $name, $slot);
				$ship->pointCostEnh = $enhvalue;
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
                id, playerid, name, phpclass, slot, enhvalue
            FROM
                tac_ship 
            WHERE
                tacgameid = ?
            "
        );

        if ($stmt) {
            $stmt->bind_param('i', $gamedata->id);
            $stmt->bind_result($id, $playerid, $name, $phpclass, $slot, $enhvalue);
            $stmt->execute();
            while ($stmt->fetch()) {
                $ship = new $phpclass($id, $playerid, $name, $slot);
				$ship->pointCostEnh = $enhvalue;
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
            //$this->flightSizeFix($ships); //Marcin Sawicki, October 2019: perhaps once there was a reason for "fixing" flight size, but I do not see it any more
            //$this->getAdaptiveArmourSettings($gamedata); //Adaptive Armor redone in a different way
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

/* no longer needed, Adaptive Armor redone
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
*/

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

/* no longer needed - I'm leaving it commented out just in case
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
*/	


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
    }//endof function getIniativeForShips
	

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


    private function getEnhencementsForShip($shipID){
	$toReturn = array();
	$stmt = $this->connection->prepare( //enhname will be used for info tooltip!
            "SELECT 
                enhid, numbertaken, enhname
            FROM 
                tac_enhancements 
            WHERE 
                shipid = ?
            "
        );
        if ($stmt)
        {
            $stmt->bind_param('i', $shipID);
            $stmt->bind_result($enhID, $numbertaken, $description);
            $stmt->execute();
            while ($stmt->fetch())
            {
		    $toReturn[] = array($enhID,$numbertaken,$description);
            }
        }
	return $toReturn;
    } //endof function getEnhencementsForShip
	
	
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

 
 
    private function getDamageForShips($gamedata, $fetchTurn)
    {
        $damageStmt = $this->connection->prepare(
            "SELECT 
                id, shipid, gameid, turn, systemid, damage, armour, shields, fireorderid, destroyed, undestroyed, pubnotes, damageclass 
            FROM
                tac_damage
            WHERE 
                gameid = ? AND turn <= ?
			ORDER BY 
				id ASC
            " //sorting guarantees that entries come in proper order - important for destroyed/undestroyed business!
        );

        if ($damageStmt) {
            $damageStmt->bind_param('ii', $gamedata->id, $fetchTurn);
            $damageStmt->bind_result($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $undestroyed, $pubnotes, $damageclass);
            $damageStmt->execute();
            while ($damageStmt->fetch()) {
                $gamedata->getShipById($shipid)->getSystemById($systemid)->setDamage(
                    new DamageEntry($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $undestroyed, $pubnotes, $damageclass)
                );
            }
            $damageStmt->close();
        }
    }


    private function getCriticalsForShips($gamedata, $fetchTurn)
    {
        $criticalStmt = $this->connection->prepare(
            "SELECT 
                id, shipid, systemid, type, turn, turnend, param 
            FROM 
                tac_critical
            WHERE 
                gameid = ? AND turn <= ? AND (turnend = 0 OR turnend >= ? )
            "
        );

        if ($criticalStmt) {
			$turnEnd = 0;
			$turnBefore = $fetchTurn - 1;//expanded to turn before - explicitly for functionality of getting force-disabled systems back up!
            //$criticalStmt->bind_param('iii', $gamedata->id, $fetchTurn, $fetchTurn);
			$criticalStmt->bind_param('iii', $gamedata->id, $fetchTurn, $turnBefore);
            $criticalStmt->bind_result($id, $shipid, $systemid, $type, $turn, $turnEnd, $param);
            $criticalStmt->execute();
            while ($criticalStmt->fetch()) {
				
				//actually the only crit needed from earlier turn is forced shutdown - and others prove to be troublesome due to current turn being set to currrent turn rather than fetched turn...
				$doAddCrit = false;
				if(($turnEnd ==0) || ($turnEnd >=$fetchTurn)) $doAddCrit = true;
				if(($type=='ForcedOfflineOneTurn') || ($type=='ForcedOfflineForTurns')) $doAddCrit = true;
				if($doAddCrit){				
					$gamedata->getShipById($shipid)->getSystemById($systemid)->setCritical(
						new $type($id, $shipid, $systemid, $type, $turn, $turnEnd, $param),
						$gamedata->turn
					);
				}
            }
            $criticalStmt->close();
        }
		
		/*old version - expanded wwhen turnend was added!
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
		*/

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
        // Optmization: Fetch ALL system data up to current turn, ordered by turn ASC.
        // We will then iterate and overwrite the data in a PHP array. 
        // This avoids the expensive correlated subquery "SELECT ... ORDER BY turn DESC LIMIT 1" for every row.
        $stmt = $this->connection->prepare(
        "SELECT 
                shipid, systemid, subsystem, data
            FROM
                tac_systemdata
            WHERE 
                gameid = ? AND turn <= ? 
            ORDER BY turn ASC
          "
        );

        if ($stmt) {
            $stmt->bind_param('ii', $gamedata->id, $fetchTurn);
            $stmt->execute();
            $stmt->bind_result(
                $shipid,
                $systemid,
                $subsystem,
                $data
            );
            
            $systemDataMap = array(); // shipid -> systemid -> subsystem -> data

            while ($stmt->fetch()) {
                 if (!isset($systemDataMap[$shipid])) $systemDataMap[$shipid] = array();
                 if (!isset($systemDataMap[$shipid][$systemid])) $systemDataMap[$shipid][$systemid] = array();
                 $systemDataMap[$shipid][$systemid][$subsystem] = $data;
            }
            $stmt->close();
            
            // Apply to ships
            foreach ($systemDataMap as $sId => $systems) {
                $ship = $gamedata->getShipById($sId);
                if (!$ship) continue;
                foreach ($systems as $sysId => $subsystems) {
                    $system = $ship->getSystemById($sysId);
                    if (!$system) continue;
                    foreach ($subsystems as $subId => $dat) {
                        $system->setSystemData($dat, $subId);
                    }
                }
            }
        }

        // Get ammo info - Same optimization
        $stmt = $this->connection->prepare(
            "SELECT 
                shipid, systemid, firingmode, ammo
            FROM 
                tac_ammo
            WHERE 
                gameid = ? AND turn <= ?
            ORDER BY turn ASC
            "
        );

        if ($stmt) {
            $stmt->bind_param('ii', $gamedata->id, $fetchTurn);
            $stmt->execute();
            $stmt->bind_result(
                $shipid,
                $systemid,
                $firingmode,
                $ammo
            );
            
            $ammoMap = array();

            while ($stmt->fetch()) {
                 if (!isset($ammoMap[$shipid])) $ammoMap[$shipid] = array();
                 if (!isset($ammoMap[$shipid][$systemid])) $ammoMap[$shipid][$systemid] = array();
                 $ammoMap[$shipid][$systemid][$firingmode] = $ammo;
            }
            $stmt->close();
            
            // Apply ammo
            foreach ($ammoMap as $sId => $systems) {
                $ship = $gamedata->getShipById($sId);
                if (!$ship) continue;
                foreach ($systems as $sysId => $modes) {
                     $system = $ship->getSystemById($sysId);
                     if (!$system) continue;
                     foreach ($modes as $mode => $amount) {
                         $system->setAmmo($mode, $amount);
                     }
                }
            }
        }	    


		//get enhancement info - optimization: single query for all ships
		$allEnhancements = $this->getEnhancementsForGame($gamedata->id);
		
		foreach ($gamedata->ships as $ship){
             $shipEnhancements = isset($allEnhancements[$ship->id]) ? $allEnhancements[$ship->id] : array();
             
			if( count($shipEnhancements) == 0 ){ //no enhancements! add empty one just to show it's been read
				$ship->enhancementOptions[] = array('NONE','-', 0,0,0,0); //[ID,readableName,numberTaken,limit,price,priceStep]
			}
			foreach($shipEnhancements as $entry){
				$ship->enhancementOptions[] = array($entry[0],$entry[2], $entry[1],0,0,0);
			}
		}
		
		//get individual notes for systems - optimization: single query
        $allNotes = $this->getIndividualNotesForGame($gamedata, $fetchTurn);
        
		foreach ($gamedata->ships as $ship){
            $shipNotes = isset($allNotes[$ship->id]) ? $allNotes[$ship->id] : array();
            
			foreach ($shipNotes as $currNote){
				$system = $ship->getSystemById($currNote->systemid);
                if ($system) // Robustness check
				    $system->addIndividualNote($currNote);
			}
			$ship->onIndividualNotesLoaded($gamedata);
		}
		
    } //endof function getSystemDataForShips

/* // BACKUP of old getSystemDataForShips (N+1 query version) - retained for safety
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
                $gamedata->getShipById($shipid)->getSystemById($systemid)->setAmmo($firingmode, $ammo);
            }
            $stmt->close();
        }	    

		//get enhancement info   
		foreach ($gamedata->ships as $ship){
			$enhArray = $this->getEnhencementsForShip($ship->id);//result: array($enhID,$numbertaken,$readablename);
			if( count($enhArray) == 0 ){ //no enhancements! add empty one just to show it's been read
				$ship->enhancementOptions[] = array('NONE','-', 0,0,0,0); //[ID,readableName,numberTaken,limit,price,priceStep]
			}
			foreach($enhArray as $entry){
				$ship->enhancementOptions[] = array($entry[0],$entry[2], $entry[1],0,0,0);
			}
		}
		
		//get individual notes for systems
		foreach ($gamedata->ships as $ship){
			$listNotes = $this->getIndividualNotesForShip($gamedata, $fetchTurn, $ship->id);	
			foreach ($listNotes as $currNote){
				$system = $ship->getSystemById($currNote->systemid);
				$system->addIndividualNote($currNote);
			}
			$ship->onIndividualNotesLoaded($gamedata);
		}
		
    }
*/

    // Optimized bulk fetcher
    private function getEnhancementsForGame($gameID){
        $toReturn = array(); // Map shipid -> array of entries
        $stmt = $this->connection->prepare(
            "SELECT 
                shipid, enhid, numbertaken, enhname
            FROM 
                tac_enhancements 
            WHERE 
                gameid = ?
            "
        );
        if ($stmt)
        {
            $stmt->bind_param('i', $gameID);
            $stmt->bind_result($shipID, $enhID, $numbertaken, $description);
            $stmt->execute();
            while ($stmt->fetch())
            {
                if (!isset($toReturn[$shipID])) $toReturn[$shipID] = array();
                $toReturn[$shipID][] = array($enhID,$numbertaken,$description);
            }
            $stmt->close();
        }
        return $toReturn;
    }

    // Optimized bulk fetcher
	public function getIndividualNotesForGame($gamedata, $turn)
	{
		$toReturn = array(); // Map shipid -> array of Note objects
		$stmt = $this->connection->prepare(
            "SELECT *
				FROM 
					tac_individual_notes
				WHERE 
					gameid = ? AND turn <= ? 
				ORDER BY turn ASC, phase ASC
			"
        );
		
		if ($stmt) {
            $stmt->bind_param('ii', $gamedata->id, $turn);
            $stmt->execute();
            $stmt->bind_result(
                $id,
                $gameid,
                $turn,
                $phase,
                $shipid_db,
                $systemid_db,
                $notekey,
                $notekey_human,
                $notevalue
            );

            while ($stmt->fetch()) {
                $entry = new IndividualNote(
					$id,
					$gameid,
					$turn,
					$phase,
					$shipid_db,
					$systemid_db,
					$notekey,
					$notekey_human,
					$notevalue
                );
                
                if (!isset($toReturn[$shipid_db])) $toReturn[$shipid_db] = array();
				$toReturn[$shipid_db][] = $entry;
            }
            $stmt->close();
        }
		
		return $toReturn;
		
	}
	
	
	
	
	
	/*
		individual system data
		ASCENDING order for easiest update
	*/
	public function getIndividualNotesForShip($gamedata, $turn, $shipID)
	{
		$toReturn = array();
		$stmt = $this->connection->prepare(
            "SELECT *
				FROM 
					tac_individual_notes
				WHERE 
					gameid = ? AND turn <= ? 
					and shipid = ? 
				ORDER BY turn ASC, phase ASC
			"
        );
		
		if ($stmt) {
            $stmt->bind_param('iii', $gamedata->id, $turn, $shipID);
            $stmt->execute();
            $stmt->bind_result(
                $id,
                $gameid,
                $turn,
                $phase,
                $shipid_db,
                $systemid_db,
                $notekey,
                $notekey_human,
                $notevalue
            );

            while ($stmt->fetch()) {
                $entry = new IndividualNote(
					$id,
					$gameid,
					$turn,
					$phase,
					$shipid_db,
					$systemid_db,
					$notekey,
					$notekey_human,
					$notevalue
                );
				$toReturn[] = $entry;
            }
            $stmt->close();
        }
		
		return $toReturn;
		
	} //endof function getIndividualNotesForSystem






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
                $damageclass,
		$resolutionOrder
            );

            while ($stmt->fetch()) {
                $entry = new FireOrder(
                    $id, $type, $shooterid, $targetid,
                    $weaponid, $calledid, $turn, $firingMode, $needed,
                    $rolled, $shots, $shotshit, $intercepted, $x, $y, $damageclass, $resolutionOrder
                );

                $entry->notes = $notes;
                $entry->pubnotes = $pubnotes;
                $gamedata->getShipById($shooterid)->getSystemById($weaponid)->setFireOrder($entry);
            }
            $stmt->close();
        }
    }

    public function submitSingleFireorder($gameid, $fireOrder)
    {
            $sql = "INSERT INTO `tac_fireorder` VALUES (null, '" . $fireOrder->type . "', " . $fireOrder->shooterid . ", " . $fireOrder->targetid . ", " . $fireOrder->weaponid . ", " . $fireOrder->calledid . ", " . $fireOrder->turn . ", "
                . $fireOrder->firingMode . ", " . $fireOrder->needed . ", " . $fireOrder->rolled . ", $gameid, '" . $fireOrder->notes . "', " . $fireOrder->shotshit . ", " . $fireOrder->shots . ", '" . $fireOrder->pubnotes . "', 0, '" . $fireOrder->x . "', '" . $fireOrder->y . "', '" . $fireOrder->damageclass . "', '" . $fireOrder->resolutionOrder . "')";

            $this->update($sql);

    }


	public function getFireOrdersForWeapon($gameid, $shooterid, $weaponid, $fetchTurn)
	{
	    $fireOrders = []; // Initialize an array to store the results

	    $stmt = $this->connection->prepare(
	        "SELECT 
	            id, type, shooterid, targetid, weaponid, calledid, turn,
	            firingmode, needed, rolled, gameid, notes, shotshit,
	            shots, pubnotes, intercepted, x, y, damageclass, resolutionorder
	        FROM 
	            tac_fireorder
	        WHERE 
	            gameid = ? AND shooterid = ? AND weaponid = ? AND turn = ?
	        ORDER BY 
	            gameid DESC"
	    );

	    if ($stmt) {
	        $stmt->bind_param('iiii', $gameid, $shooterid, $weaponid, $fetchTurn);
	        $stmt->execute();

	        $stmt->store_result(); // Store the result set to access the number of rows
	        if ($stmt->num_rows == 0) {
	            echo "No matching rows found for the query.";
	        }

	        $stmt->bind_result(
	            $id, $type, $shooterid, $targetid, $weaponid, $calledid,
	            $turn, $firingMode, $needed, $rolled, $gameid, $notes,
	            $shotshit, $shots, $pubnotes, $intercepted, $x, $y,
	            $damageclass, $resolutionOrder
	        );

	        while ($stmt->fetch()) {
	            $entry = new FireOrder(
	                $id, $type, $shooterid, $targetid,
	                $weaponid, $calledid, $turn, $firingMode, $needed,
	                $rolled, $shots, $shotshit, $intercepted, $x, $y, $damageclass, $resolutionOrder
	            );

	            $entry->notes = $notes;
	            $entry->pubnotes = $pubnotes;
	            
	            // Add the entry to the array
	            $fireOrders[] = $entry;
	        }
	        $stmt->close();
	    } else {
	        echo "Failed to prepare statement.\n";
	    }

	    return $fireOrders; // Return the array of FireOrder objects
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

    public function updateSlotSurrendered($gameid, $playerid, $surrendered)
    {
        try {
            if ($stmt = $this->connection->prepare(
                "UPDATE 
                    tac_playeringame 
                SET
                    surrendered = ?
                WHERE 
                    gameid = ? AND playerid = ?"
            )) {
                $stmt->bind_param('iii', $surrendered, $gameid, $playerid);
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
                DATE_ADD(p.lastactivity, INTERVAL 3 MONTH) < NOW()
            OR
                (DATE_ADD(p.lastactivity, INTERVAL 5 DAY) < NOW() 
                AND
                g.status = 'LOBBY')

        ");
		//28.03.2024: increased delete time for inactive games to 5 days: useful for test games, as well as allows games to be picked up after weekend!
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
		
			//unit enhancements
            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_enhancements
                WHERE
                    gameid = ?"
            );
            $this->executeGameDeleteStatement($stmt, $ids);
			
			//individual system notes
            $stmt = $this->connection->prepare(
                "DELETE FROM 
                    tac_individual_notes
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

    public function removePowerEntriesForTurn($gameid, $shipid, $systemid, $turn)
    {

        $stmt = $this->connection->prepare(
            "DELETE FROM tac_power
            WHERE gameid = ?
            AND shipid = ?
            AND systemid = ?
            AND turn = ?"
        );

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->connection->error);
        }

        if (!$stmt->bind_param('iiii', $gameid, $shipid, $systemid, $turn)) {
            throw new Exception("Bind failed: " . $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // Check how many rows were actually deleted
        if ($stmt->affected_rows === 0) {
            // No row matched those IDs
            error_log("No matching row found for gameid=$gameid, shipid=$shipid, systemid=$systemid, turn=$turn");
        } else {
            // At least one row was deleted
            error_log("Deleted {$stmt->affected_rows} row(s) for gameid=$gameid, shipid=$shipid, systemid=$systemid, turn=$turn");
        }

        $stmt->close();
    }

/*
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
*/
//New hopefully better function.
public function getLastTimeChatChecked($userid, $gameid)
{
    $lastTime = null;

    $stmt = $this->connection->prepare("
        SELECT last_checked
        FROM player_chat
        WHERE playerid = ? AND gameid = ?
    ");

    if ($stmt) {
        $stmt->bind_param('ii', $userid, $gameid);
        $stmt->execute();
        $stmt->bind_result($lastTimeChecked);
        if ($stmt->fetch()) {
            $lastTime = $lastTimeChecked;
        }
        $stmt->close();
    }

    return $lastTime;
}

/*
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
*/
//new version
public function setLastTimeChatChecked($userid, $gameid)
{
    $time = null;

    // Check if an entry exists
    $stmt = $this->connection->prepare("
        SELECT last_checked
        FROM player_chat
        WHERE playerid = ? AND gameid = ?
    ");

    if ($stmt) {
        $stmt->bind_param('ii', $userid, $gameid);
        $stmt->execute();
        $stmt->bind_result($time);
        $stmt->fetch();
        $stmt->close();
    }

    // Update if exists, otherwise insert
    if ($time !== null) {
        $stmt = $this->connection->prepare("
            UPDATE player_chat
            SET last_checked = NOW()
            WHERE playerid = ? AND gameid = ?
        ");
    } else {
        $stmt = $this->connection->prepare("
            INSERT INTO player_chat (playerid, gameid, last_checked)
            VALUES (?, ?, NOW())
        ");
    }

    if ($stmt) {
        $stmt->bind_param('ii', $userid, $gameid);
        $stmt->execute();
        $stmt->close();
    }
}


/*
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
*/
//New verion
	public function submitChatMessage($userid, $message, $gameid = 0)
	{
		$stmt = $this->connection->prepare("
			INSERT INTO chat (userid, username, gameid, time, message)
			VALUES (?, (SELECT username FROM player WHERE id = ?), ?, NOW(), ?)
		");
		
		$id = -1;

		if ($stmt) {
			$stmt->bind_param('iiis', $userid, $userid, $gameid, $message);
			$stmt->execute();
			$stmt->close();
			$id = $this->getLastInstertID();
		}
		
		return $id;
	}

    public function getChatMessages($lastid, $gameid = 0)
    {
        $messages = array();
        
        // Critical Optimization:
        // If lastid is 0 (initial load or reset), ONLY fetch the last 20 messages.
        // This prevents the "memory limit" cracshes seen when a client reconnects and tries to fetch 'all' history.
        // The default LIMIT 50 was causing issues on CloudLinux due to large JSON payloads.
        $limit = ($lastid == 0) ? 15 : 25;

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
            LIMIT ?;
        ");

        if ($stmt) {
            $stmt->bind_param('iii', $gameid, $lastid, $limit);
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
    public function registerLadderPlayer($playerid)
    {
        // Check if already registered
        $sql = "SELECT playerid FROM tac_ladder_rankings WHERE playerid = $playerid";
        $result = $this->query($sql);
        if ($result && sizeof($result) > 0) {
           throw new Exception("You are already registered for the ladder.");
        }

        // Insert with default rating 100
        $sql = "INSERT INTO tac_ladder_rankings (playerid, rating) VALUES ($playerid, 100)";
        $this->insert($sql);
    }

    public function removeLadderPlayer($playerid)
    {
        $playerid = (int)$playerid;
        // Delete from rankings
        $sql = "DELETE FROM tac_ladder_rankings WHERE playerid = $playerid";
        $this->update($sql);

        // Delete from games history
        $sql = "DELETE FROM tac_ladder_games WHERE playerid = $playerid";
        $this->update($sql);
    }

    public function isLadderPlayer($playerid)
    {
        $playerid = (int)$playerid;
        $sql = "SELECT playerid FROM tac_ladder_rankings WHERE playerid = $playerid";
        $result = $this->query($sql);
        return ($result && sizeof($result) > 0);
    }

    public function registerLadderResult($gameid, $playerid, $status)
    {
        $sql = "INSERT INTO tac_ladder_games (gameid, playerid, status) VALUES ($gameid, $playerid, '$status')";
        $this->insert($sql);

        // Update Rating: +1 for WIN, -1 for LOSS
        $change = 0;
        if ($status === "WIN") $change = 1;
        else if ($status === "LOSS") $change = -1;

        if ($change != 0) {
            // Upsert: If player exists, add change. If not, start at 100 + change.
            $sql = "INSERT INTO tac_ladder_rankings (playerid, rating) VALUES ($playerid, 100 + ($change)) 
                    ON DUPLICATE KEY UPDATE rating = rating + ($change)";
            $this->insert($sql); 
        }
    }

    public function getLadderHistory($playerid)
    {
        $sql = "SELECT g.id, g.name, lg.status, p_opp.username as opponent_name, p_opp.id as opponent_id
                FROM tac_ladder_games lg
                JOIN tac_game g ON lg.gameid = g.id
                LEFT JOIN tac_ladder_games lg_opp ON lg_opp.gameid = g.id AND lg_opp.playerid != lg.playerid
                LEFT JOIN player p_opp ON lg_opp.playerid = p_opp.id
                WHERE lg.playerid = $playerid
                ORDER BY g.id DESC
                LIMIT 20";
        return $this->query($sql);
    }

    public function getLadderStandings()
    {
        $sql = "SELECT r.playerid, r.rating, p.username,
                (SELECT COUNT(*) FROM tac_ladder_games g WHERE g.playerid = r.playerid AND g.status = 'WIN') as wins,
                (SELECT COUNT(*) FROM tac_ladder_games g WHERE g.playerid = r.playerid AND g.status = 'LOSS') as losses
                FROM tac_ladder_rankings r
                LEFT JOIN player p ON r.playerid = p.id
                WHERE 
                    (SELECT COUNT(*) FROM tac_ladder_games lg WHERE lg.playerid = r.playerid) = 0
                    OR
                    EXISTS (
                        SELECT 1 FROM tac_ladder_games lg 
                        JOIN tac_game g ON lg.gameid = g.id 
                        WHERE lg.playerid = r.playerid
                    )
                ORDER BY r.rating DESC";
                
        return $this->query($sql);
    }
}

