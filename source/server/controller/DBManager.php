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

        // Plain Exception, not the old CustomException: that class was never defined
        // anywhere, so a failed connect threw "Class CustomException not found" — an
        // Error, which catch(Exception) blocks in Manager cannot catch — masking the
        // real DB error behind an uncatchable fatal.
        if (!$this->connection = mysqli_connect($host, $username, $password, $database, $port))
            throw new Exception("DBManager:Construct, connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")", 300);

        if (!mysqli_select_db($this->connection, $database))
            throw new Exception("DBManager:Construct, database select failed: " . mysqli_error($this->connection) . " (" . mysqli_errno($this->connection) . ")", 300);

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
        $sql = "INSERT INTO `B5CGM`.`tac_ship` (playerid, tacgameid, name, phpclass, slot, enhvalue) VALUES($userid, $gameid, '" . $this->DBEscape($ship->name) . "', '" . $ship->phpclass . "', $ship->slot, $ship->pointCostEnh)";
		*/
		/* pointCostSysEnh is the third bucket (WEAPON_ENHANCEMENTS_PLAN.md D5) - per-system refits.
		   It belongs in tac_ship.enhvalue with the other two: that column is what the fleet list
		   reads back as the ship's enhancement spend, and a refit is spend. BuyingGamePhase sets
		   it from the SERVER-derived total before calling this, never from the client's claim. */
		$enhCostTotal = $ship->pointCostEnh + $ship->pointCostEnh2 + $ship->pointCostSysEnh;
		/* ⚠️ NAMED COLUMNS, not the positional `VALUES(null, …)` this used to be
		   (REINFORCEMENTS_PLAN.md trap 1). The positional form silently depended on tac_ship
		   never gaining a column, and broke every ship insert in the game with a column-count
		   error the moment db/reinforcements.sql added three. rolling, rolled and the three
		   campaign* columns are still written with the same literal 0s the positional form gave
		   them - named rather than dropped in favour of the table defaults, so the row this
		   writes stays byte-identical to the one it wrote before.
		   `reinforcement` is written here because this is the ONE insert path for every ship in
		   the game (Manager::insertSingleShip routes through it), so a mid-game spawn gets a
		   correct 0 for free. arrivalturn/arrivalvia are deliberately left NULL: a reinforcement
		   is born in hyperspace and unassigned, and both are written later by the server alone. */
		$isReinforcement = !empty($ship->reinforcement) ? 1 : 0;
        $sql = "INSERT INTO `tac_ship` (playerid, tacgameid, name, phpclass, rolling, rolled, campaignX, campaignY, campaigngameid, slot, enhvalue, reinforcement) "
             . "VALUES($userid, $gameid, '" . $this->DBEscape($ship->name) . "', '" . $ship->phpclass . "', 0, 0, 0, 0, 0, $ship->slot, $enhCostTotal, $isReinforcement)";

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


	/* PER-SYSTEM enhancement, written at buy time from BuyingGamePhase::process
	   (WEAPON_ENHANCEMENTS_PLAN.md §4.5). Prepared, unlike its ship-level twin above: the
	   two string columns both come from server-side tables (the registry's label and the
	   system's ->name), but there is no reason to reintroduce string interpolation on a
	   brand-new path.
	   $enhvalue is the price the SERVER derived (D4) - the client's claim is discarded
	   before this is ever called - and it is stored so a refund can be exact.
	   $sysname is the D13 integrity check, verified against the rebuilt ship on load. */
	public function submitSystemEnhancement($gameid, $shipid, $systemid, $sysname, $enhid, $numbertaken, $enhname, $enhvalue)
	{
		try{
			$stmt = $this->connection->prepare(
				"INSERT INTO `tac_sys_enhancements`
					(gameid, shipid, systemid, sysname, enhid, numbertaken, enhname, enhvalue)
				 VALUES (?, ?, ?, ?, ?, ?, ?, ?)
				 ON DUPLICATE KEY UPDATE
					numbertaken = VALUES(numbertaken), enhvalue = VALUES(enhvalue)"
			);
			if (!$stmt) throw new Exception("DB error in submitSystemEnhancement (prepare): " . $this->connection->error);
			//'d' on enhvalue - DECIMAL column, and a price can be fractional.
			$gameid      = (int)$gameid;
			$shipid      = (int)$shipid;
			$systemid    = (int)$systemid;
			$sysname     = (string)$sysname;
			$enhid       = (string)$enhid;
			$numbertaken = (int)$numbertaken;
			$enhname     = (string)$enhname;
			$enhvalue    = (float)$enhvalue;
			$stmt->bind_param('iiissisd', $gameid, $shipid, $systemid, $sysname, $enhid, $numbertaken, $enhname, $enhvalue);
			$stmt->execute();
			$stmt->close();
		}catch(Exception $e) {
			$this->endTransaction(true);
			throw $e;
		}
	} //endof function submitSystemEnhancement


    public function submitFlightSize($gameid, $shipid, $flightSize)
    {
        $sql = "INSERT INTO `tac_flightsize` (gameid, shipid, flightsize)
            VALUES ($gameid, $shipid, $flightSize)";

        $id = $this->insert($sql);
    }

    /* Hangar Ops Stage 21.5: update an existing ship row's persisted enhancement
     * cost (tac_ship.enhvalue, the source of $ship->pointCostEnh on load). Used
     * when a partial launch carries a proportional share of a docked flight's
     * enhancement onto the launched K-flight — the docked remnant's stored value
     * must drop by that share so the two rows don't double-count it in fleetList. */
    public function submitEnhValue($shipid, $enhValue)
    {
        $stmt = $this->connection->prepare("UPDATE `tac_ship` SET enhvalue = ? WHERE id = ?");
        //'d' — enhvalue is DECIMAL; see submitSavedShip.
        $stmt->bind_param('di', $enhValue, $shipid);
        $stmt->execute();
        $stmt->close();
    }


    /* REINFORCEMENTS_PLAN.md §3.1 / Stage 5 - which opener's jump point exit this unit is
     * riding through. NULL clears the berth (a withdrawn declaration, a manifest the player
     * un-ticked, or a claim the server did not believe).
     *
     * The opener and NOT the vortex, deliberately: the vortex does not exist when the manifest is
     * named - it is created two phases later, and for a gate it may never be created at all if the
     * claim is lost - so keying on the opener makes the refund automatic. A manifest that never
     * gets a vortex is simply never stamped with an arrival turn.
     *
     * ⚠️ arrivalturn is NOT settable from here or from anywhere a POST can reach. It is written by
     * the end-of-formation-turn deviation sweep alone; see db/reinforcements.sql.
     *
     * 'i' with an explicit null: bind_param sends a PHP null as SQL NULL for an integer parameter,
     * which is what clears the column - do not "fix" it to 0, which is a real ship id. */
    public function setShipArrivalVia($shipid, $openerid)
    {
        $stmt = $this->connection->prepare("UPDATE `tac_ship` SET arrivalvia = ? WHERE id = ?");
        $via = ($openerid === null) ? null : (int)$openerid;
        $id = (int)$shipid;
        $stmt->bind_param('ii', $via, $id);
        $stmt->execute();
        $stmt->close();
    }

    /* REINFORCEMENTS_PLAN.md section 3.1 / Stage 6 - THE TURN THIS UNIT COMES OUT OF HYPERSPACE.
     *
     * ⚠️⚠️ WRITTEN FROM EXACTLY ONE PLACE - JumpEngine::stampExitManifests, at the end of the
     * turn its exit formed - AND NO POST CAN REACH IT. Manager::getShipsFromJSON whitelists
     * arrivalVia and deliberately not this (see the note there): a player who could name their own
     * arrival turn would be placing units on a board with no vortex on it, in a Deployment phase
     * nothing granted them.
     *
     * Setting it is what ENDS the unit's hyperspace life: BaseShip::isReinforcement() is
     * `reinforcement AND arrivalturn IS NULL`, so from this write on the unit is an ordinary one
     * with a late deploy turn, visible to the enemy from the turn it arrives.
     *
     * ⭐ AND IT TAKES NULL, which Stage 7 is what added (an earlier draft of this comment said the
     * write was one-way). §2.6's one-way rule is about the DOORWAY - an exit cannot be jumped
     * out of - and says nothing about a unit that never walked through one. Placement is optional
     * (§2.4), so DeploymentGamePhase::releaseUnplacedReinforcements clears BOTH arrival fields on
     * anything the player left behind and the unit goes back to being an ordinary reinforcement
     * waiting in hyperspace, concealed again, with nothing spent. Clearing arrivalvia alone would
     * leave it reading as a ship that deployed on a turn now past.
     *
     * mysqli binds a PHP null as SQL NULL whatever the type character says, which is the same thing
     * setShipArrivalVia above relies on - do not "fix" the 'i' to something else. */
    public function setShipArrivalTurn($shipid, $turn)
    {
        $stmt = $this->connection->prepare("UPDATE `tac_ship` SET arrivalturn = ? WHERE id = ?");
        $arrival = ($turn === null) ? null : (int)$turn;
        $id = (int)$shipid;
        $stmt->bind_param('ii', $arrival, $id);
        $stmt->execute();
        $stmt->close();
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
        //Bulk purchases (mines) are ONE row carrying a count, exactly as the lobby buys
        //them. Before this column existed the count was silently dropped and a saved
        //fleet of 10 mines reloaded as 1.
        $bulkbuy = max(1, (int)($ship->bulkBuy ?? 1));
		//Third bucket, same reasoning as submitShip above: a saved fleet's enhvalue has to carry
		//the per-system refits or reloading it prices the ship short (D5).
		$enhCostTotal = $ship->pointCostEnh + $ship->pointCostEnh2 + $ship->pointCostSysEnh;
		/* REINFORCEMENTS_PLAN.md §0 - A SAVED FLEET DOES REMEMBER WHICH UNITS WERE REINFORCEMENTS
		   (user request 2026-08-28; this reverses the original ruling, which was that it would
		   not). Re-flagging a dozen rows by hand after every load was the whole of the cost.

		   Only the PURCHASE-TIME flag travels. `arrivalturn` and `arrivalvia` are in-play state
		   written by the server during a battle and mean nothing in a fleet list, so they get no
		   column here - a reloaded reinforcement is always back in hyperspace, exactly as if it
		   had just been bought.

		   Reading as 0 on every row written before the column existed is the old behaviour
		   unchanged, and loading into a game WITHOUT the rule still lands everything front-line
		   because gamedata.isReinforcementRow gates on the rule as well as the flag. */
		$reinforcement = !empty($ship->reinforcement) ? 1 : 0;

        $sql = "INSERT INTO tac_saved_ship
                (userid, listid, name, phpclass, flightsize, bulkbuy, enhvalue, reinforcement)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception("DB prepare failed: " . $this->connection->error);
        }

        //enhvalue binds as 'd', not 'i': a per-unit enhancement cost is not always whole
        //(MINE_DMG is 0.5/level), and truncating it here made a fleet reload dearer than it
        //was bought. See db/fractionalEnhancementValue.sql.
        $stmt->bind_param(
            "iissiidi",
            $userid,
            $listId,
            $shipName,
            $ship->phpclass,
            $flightsize,
            $bulkbuy,
            $enhCostTotal,
            $reinforcement
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

	/* PER-SYSTEM enhancements for a saved fleet (WEAPON_ENHANCEMENTS_PLAN.md §4.7).
	   Batched like submitSavedDamageRows rather than one call per row: a fleet of refitted
	   ships is dozens of rows, and the chunking/binding rules are already solved there.
	   @param array $rows [[shipid, systemid, sysname, enhid, numbertaken, enhname, enhvalue], …]
	   Every value here is server-derived; the client's prices never reach this method (D4). */
	public function submitSavedSystemEnhancementRows($listid, array $rows)
	{
		foreach (array_chunk($rows, self::SAVED_ROW_CHUNK) as $chunk) {
			$types = '';
			$values = array();
			$placeholders = array();

			foreach ($chunk as $row) {
				$placeholders[] = '(?, ?, ?, ?, ?, ?, ?, ?)';
				$types .= 'iiissisd';
				$values[] = (int)$listid;
				$values[] = (int)$row[0];
				$values[] = (int)$row[1];
				$values[] = (string)$row[2];
				$values[] = (string)$row[3];
				$values[] = (int)$row[4];
				$values[] = (string)$row[5];
				$values[] = (float)$row[6];
			}

			$this->executeBatchInsert(
				"INSERT INTO tac_saved_sysenh
					(listid, shipid, systemid, sysname, enhid, numbertaken, enhname, enhvalue)
				 VALUES " . implode(', ', $placeholders) . "
				 ON DUPLICATE KEY UPDATE
					numbertaken = VALUES(numbertaken), enhvalue = VALUES(enhvalue), sysname = VALUES(sysname)",
				$types, $values, 'submitSavedSystemEnhancementRows'
			);
		}
	}

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
        //hasDamage / hasCrits drive the two INDEPENDENT load checkboxes (each is hidden
        //when its flag is false) and the dropdown badge. Two EXISTS subqueries, so no
        //extra round trip.
        $stmt = $this->connection->prepare(
            "SELECT l.id, l.name, l.userid, l.points, l.isPublic,
                EXISTS(SELECT 1 FROM tac_saved_damage d WHERE d.listid = l.id) AS hasDamage,
                EXISTS(SELECT 1 FROM tac_saved_crit  c WHERE c.listid = l.id) AS hasCrits
            FROM tac_saved_list l
            WHERE l.userid = ? OR l.userid = 0
            ORDER BY l.userid DESC, l.name ASC" // optional: user fleets first
        );
        if ($stmt) {
            $stmt->bind_param('i', $userid);
            $stmt->execute();
            $stmt->bind_result($id, $name, $fleetUserId, $points, $isPublic, $hasDamage, $hasCrits); // renamed to avoid variable clash
            while ($stmt->fetch()) {
                $savedFleets[] = [
                    'id' => $id,
                    'name' => $name,
                    'userid' => $fleetUserId,
                    'points' => $points,
                    'isPublic' => $isPublic,
                    'hasDamage' => (bool) $hasDamage,
                    'hasCrits' => (bool) $hasCrits
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
            "SELECT l.id, l.name, l.userid, l.points, l.isPublic,
                EXISTS(SELECT 1 FROM tac_saved_damage d WHERE d.listid = l.id) AS hasDamage,
                EXISTS(SELECT 1 FROM tac_saved_crit  c WHERE c.listid = l.id) AS hasCrits
            FROM tac_saved_list l
            WHERE l.id = ?"
        );

        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $stmt->bind_result($id, $name, $userid, $points, $isPublic, $hasDamage, $hasCrits);

            if ($stmt->fetch()) {
                $savedFleet = [
                    'id' => $id,
                    'name' => $name,
                    'userid' => $userid,
                    'points' => $points,
                    'isPublic' => (bool) $isPublic,
                    'hasDamage' => (bool) $hasDamage,
                    'hasCrits' => (bool) $hasCrits
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
                id, userid, name, phpclass, flightsize, bulkbuy, enhvalue, reinforcement
            FROM
                tac_saved_ship
            WHERE
                listid = ?
            "
        );

        if ($stmt) {
            $stmt->bind_param('i', $listid);
            $stmt->bind_result($shipid, $userid, $name, $phpclass, $flightsize, $bulkbuy, $enhvalue, $reinforcement);
            $stmt->execute();
            while ($stmt->fetch()) {
                $ship = new $phpclass($shipid, $userid, $name, 1);
                if($ship instanceof FighterFlight) $ship->flightSize = $flightsize;
                //Bulk purchases come back as the ONE unit the lobby bought plus its count,
                //not as N separate units. Rows written before the column existed default
                //to 1, which is exactly how they always behaved.
                //isBulkBought() is the single definition (mines + OSATs), mirrored on the
                //client by gamedata.isBulkRow - if the two disagreed, a saved OSAT bulk
                //would reload as a single unit.
                if ($ship->isBulkBought()) $ship->bulkBuy = max(1, (int)$bulkbuy);
				//(float): mysqli hands a DECIMAL back as a STRING, and this value is
				//json_encoded to the client, where `pointCost + pointCostEnh` would then
				//CONCATENATE instead of adding. See db/fractionalEnhancementValue.sql.
				$ship->pointCostEnh = (float)$enhvalue;
				/* REINFORCEMENTS_PLAN.md §0 - which units this fleet was saved with in hyperspace
				   (user request 2026-08-28). $reinforcement is a public BaseShip property, so it
				   rides the loadSavedFleet.php payload through json_encode with no extra plumbing;
				   the lobby re-applies it in gamedata.loadSavedFleet, still gated on the game
				   actually carrying the rule.
				   $arrivalTurn stays null - a reloaded reinforcement is back in hyperspace, never
				   mid-arrival - so isReinforcement() answers true, which is what a fresh purchase
				   would do too. */
				$ship->reinforcement = (bool)$reinforcement;
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

    /* ---------------------------------------------------------------- *
     *  Saved-fleet battle damage & criticals (PREBATTLE_DAMAGE_PLAN §4.5)
     *  $kind is PreBattleDamage::KIND_SYSTEM (0, $ref = systemid),
     *  KIND_FIGHTER (1, $ref = fighter ordinal) or KIND_MINE (2, $ref =
     *  mine ordinal).
     *
     *  ⚠️ BOTH SIDES ARE WHOLE-FLEET, keyed by listid, deliberately.
     *  The first cut was per SHIP on the read side and per ROW on the
     *  write side, which is what the shape of the data suggests - but a
     *  fleet saved out of a bloody 20-ship battle is several hundred
     *  damaged systems, so that was several hundred prepare/execute round
     *  trips in ONE request. tac_saved_damage/tac_saved_crit both carry
     *  listid with an index on it, so the whole fleet is two statements
     *  each way. Matters on the live LiteSpeed workers, which have a hard
     *  per-request budget.
     * ---------------------------------------------------------------- */

    /* Rows per INSERT. A fleet is normally well under one chunk; chunking exists so a
       pathological fleet cannot approach MySQL's 65,535-placeholder ceiling or build a
       statement larger than max_allowed_packet. */
    const SAVED_ROW_CHUNK = 200;

    /**
     * @param array $rows [[shipid, kind, ref, damage, destroyed], …]
     */
    public function submitSavedDamageRows($listid, array $rows)
    {
        foreach (array_chunk($rows, self::SAVED_ROW_CHUNK) as $chunk) {
            $types = '';
            $values = array();
            $placeholders = array();

            foreach ($chunk as $row) {
                $placeholders[] = '(?, ?, ?, ?, ?, ?)';
                $types .= 'iiiiii';
                $values[] = (int)$listid;
                $values[] = (int)$row[0];
                $values[] = (int)$row[1];
                $values[] = (int)$row[2];
                $values[] = (int)$row[3];
                $values[] = !empty($row[4]) ? 1 : 0;
            }

            $this->executeBatchInsert(
                "INSERT INTO tac_saved_damage
                    (listid, shipid, kind, ref, damage, destroyed)
                 VALUES " . implode(', ', $placeholders) . "
                 ON DUPLICATE KEY UPDATE
                    damage = VALUES(damage), destroyed = VALUES(destroyed)",
                $types, $values, 'submitSavedDamageRows'
            );
        }
    }

    /**
     * @param array $rows [[shipid, kind, ref, type, amount, param], …]
     *
     * `param` is the magnitude of a PARAM-CARRYING critical (DamageReductionReduced), or
     * null for the other 40-odd classes. PreBattleDamage::$paramCriticals is the
     * allow-list and sanitiseParam has already forced it to a bounded integer.
     */
    public function submitSavedCritRows($listid, array $rows)
    {
        foreach (array_chunk($rows, self::SAVED_ROW_CHUNK) as $chunk) {
            $types = '';
            $values = array();
            $placeholders = array();

            foreach ($chunk as $row) {
                $param = (isset($row[5]) && $row[5] !== '') ? (int)$row[5] : null;
                $placeholders[] = '(?, ?, ?, ?, ?, ?, ?)';
                $types .= 'iiiisii';
                $values[] = (int)$listid;
                $values[] = (int)$row[0];
                $values[] = (int)$row[1];
                $values[] = (int)$row[2];
                $values[] = (string)$row[3];
                $values[] = (int)$row[4];
                //'i' binds a PHP null as SQL NULL, which is what an ordinary critical wants.
                $values[] = $param;
            }

            $this->executeBatchInsert(
                "INSERT INTO tac_saved_crit
                    (listid, shipid, kind, ref, type, amount, param)
                 VALUES " . implode(', ', $placeholders) . "
                 ON DUPLICATE KEY UPDATE
                    amount = VALUES(amount), param = VALUES(param)",
                $types, $values, 'submitSavedCritRows'
            );
        }
    }

    /* Prepare + bind + execute one multi-row INSERT.
       ⚠️ call_user_func_array with an array of REFERENCES, not `bind_param($types,
       ...$values)`: bind_param declares its arguments by reference, and unpacking a plain
       array hands it temporaries. It happens to work on current PHP, but it is exactly the
       kind of thing that changes between an 8.2 dev container and whatever lsphp the live
       server is on - and this path writes a player's fleet. */
    private function executeBatchInsert($sql, $types, array $values, $context)
    {
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) throw new Exception("DB error in $context (prepare): " . $this->connection->error);

        $refs = array(&$types);
        foreach ($values as $i => $ignored) $refs[] = &$values[$i];
        call_user_func_array(array($stmt, 'bind_param'), $refs);

        $stmt->execute();
        $stmt->close();
    }

    /**
     * Every damage row in a fleet, as shipid => [[kind, ref, damage, destroyed], …].
     * ONE query for the whole fleet - see the note above.
     */
    public function getSavedDamageForList($listid)
    {
        $byShip = array();
        $stmt = $this->connection->prepare(
            "SELECT
                shipid, kind, ref, damage, destroyed
            FROM
                tac_saved_damage
            WHERE
                listid = ?
            "
        );
        if ($stmt)
        {
            $stmt->bind_param('i', $listid);
            $stmt->bind_result($shipid, $kind, $ref, $damage, $destroyed);
            $stmt->execute();
            while ($stmt->fetch())
            {
                $byShip[$shipid][] = array($kind, $ref, $damage, $destroyed);
            }
            $stmt->close();
        }
        return $byShip;
    }

    /**
     * Every critical row in a fleet, as shipid => [[kind, ref, type, amount, param], …].
     */
    public function getSavedCritsForList($listid)
    {
        $byShip = array();
        $stmt = $this->connection->prepare(
            "SELECT
                shipid, kind, ref, type, amount, param
            FROM
                tac_saved_crit
            WHERE
                listid = ?
            "
        );
        if ($stmt)
        {
            $stmt->bind_param('i', $listid);
            $stmt->bind_result($shipid, $kind, $ref, $type, $amount, $param);
            $stmt->execute();
            while ($stmt->fetch())
            {
                //param is NULL for every class but the param-carrying ones; rows
                //written before the column existed simply read back as NULL.
                $byShip[$shipid][] = array($kind, $ref, $type, $amount, $param);
            }
            $stmt->close();
        }
        return $byShip;
    }

    /**
     * Every PER-SYSTEM enhancement row in a fleet, as
     * shipid => [[systemid, sysname, enhid, numbertaken, enhname, enhvalue], …].
     * ONE query for the whole fleet, like getSavedDamageForList above.
     * ⚠️ enhvalue is DECIMAL -> PHP string; cast at the point of use. Nothing here trusts
     * the stored price anyway - loadSavedFleet re-derives it (D4, plan §4.7.1).
     */
    public function getSavedSystemEnhancementsForList($listid)
    {
        $byShip = array();
        $stmt = $this->connection->prepare(
            "SELECT
                shipid, systemid, sysname, enhid, numbertaken, enhname, enhvalue
            FROM
                tac_saved_sysenh
            WHERE
                listid = ?
            "
        );
        if ($stmt)
        {
            $stmt->bind_param('i', $listid);
            $stmt->bind_result($shipid, $systemid, $sysname, $enhid, $numbertaken, $enhname, $enhvalue);
            $stmt->execute();
            while ($stmt->fetch())
            {
                $byShip[$shipid][] = array($systemid, $sysname, $enhid, $numbertaken, $enhname, $enhvalue);
            }
            $stmt->close();
        }
        return $byShip;
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

            /* ⭐ The ship ids FIRST, then every table keyed by them, THEN the ships themselves.
               tac_ship is the only one of these tables with a player/slot column - all the rest are
               keyed by (gameid, shipid) with no foreign key - so deleting the ships alone leaves
               ORPHAN rows behind. That is not merely untidy: MariaDB's InnoDB does not persist the
               AUTO_INCREMENT counter, so after a server restart tac_ship.id resumes at MAX(id)+1 and
               the NEXT fleet bought into this game can be handed the exact ids the cleared one had.
               The orphans then re-attach to unrelated ships and resolve POSITIONALLY by systemid -
               refits bought on a Primus turned up on an Omega's heavy lasers (game 4302, 2026-08-17),
               and damage, criticals, ammo and flight sizes would come back the same way.
               deleteGames() clears the same tables game-wide; this is its per-slot twin. */
            $shipIds = array();
            $findShips = "SELECT id FROM `tac_ship` WHERE tacgameid = $gameid AND playerid = $userid";
            if ($slotid)
                $findShips .= " AND slot = $slotid";

            $shipRows = $this->query($findShips);
            if ($shipRows) {
                foreach ($shipRows as $row) {
                    $shipIds[] = (int)$row->id;
                }
            }

            if (count($shipIds) > 0) {
                //Ints straight out of the DB, so the list is safe to interpolate.
                $idList = implode(',', $shipIds);

                //Rows that BELONG to a removed ship.
                $shipKeyedTables = array(
                    'tac_ammo', 'tac_critical', 'tac_damage', 'tac_enhancements', 'tac_ew',
                    'tac_flightsize', 'tac_individual_notes', 'tac_iniative', 'tac_power',
                    'tac_shipmovement', 'tac_systemdata', 'tac_sys_enhancements'
                );
                foreach ($shipKeyedTables as $table) {
                    $this->update("DELETE FROM `$table` WHERE shipid IN ($idList)");
                }

                /* Rows that POINT AT a removed ship rather than belonging to it. A slot can only be
                   left from the lobby, so in practice there are none - but the recycled-id problem
                   above would mis-attribute them just as readily if that ever changes. */
                $this->update("DELETE FROM `tac_ew` WHERE targetid IN ($idList)");
                $this->update("DELETE FROM `tac_fireorder` WHERE shooterid IN ($idList) OR targetid IN ($idList)");
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
        //Name the columns explicitly: a column-less INSERT ... VALUES breaks at
        //prepare() time with "Column count doesn't match value count" the moment
        //tac_game gains a column.
        $stmt = $this->connection->prepare("
            INSERT INTO
                tac_game
                (name, turn, phase, activeship, background, points, status,
                 slots, creator, submitLock, gamespace, rules, description)
            VALUES
            (
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

        $gameid = null;
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
            if ($stmt->execute())
                $gameid = $this->getLastInstertID();
            $stmt->close();
        }

        //Without this the failure is silent: $gameid stays unset and createSlots()
        //below would go on to write orphan slots against it.
        if (!$gameid)
            throw new Exception("DBManager:createGame, insert failed: " . $this->connection->error);

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
					$param = $critical->param;
					if (is_array($param) || is_object($param)) {
						$param = json_encode($param);
					}
					$param = $this->DBEscape($param);

					$sql = "INSERT INTO `tac_critical` VALUES(null, $gameid, " . $critical->shipid . ", " . $critical->systemid . ",'" . $critical->phpclass . "'," . $critical->turn . ", " . $critical->turnend . ",'" . $param . "')";
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
		resolutionorder = ?,
		type = ?
            WHERE
                id = ?
            "
        );

        if ($stmt) {
            foreach ($fireOrders as $fire) {
                $stmt->bind_param(
                    'iiiissiiiiiisi',
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
		    $fire->type,
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

            //Corrupt order rejected at submit-time validation (stale client
            //blueprint — weaponid maps to a missing/non-weapon system). Never
            //persist it; it would crash the next game load. See
            //Firing::validateFireOrders.
            if (!empty($fire->rejected))
                continue;

  		if (($fire->type == "ballistic") && ($phase != 1) &&  ($fire->addToDB != true)) //28 Sept 2023 - Amended to enable Multimissile to shows multiple shots in Combat Log.
                continue;

            if ($fire->type != "ballistic" && $phase == 1)
                continue;

  		    if (($fire->type == "prefiring") && ($phase != 5))
                continue;

            if ($fire->type != "prefiring" && $phase == 5)
                continue;            

            $c = $this->connection;
            $sql = "INSERT INTO `tac_fireorder` VALUES (null, '" . $c->real_escape_string($fire->type ?? '') . "', " . $fire->shooterid . ", " . $fire->targetid . ", " . $fire->weaponid . ", " . $fire->calledid . ", " . $fire->turn . ", "
                . $fire->firingMode . ", " . $fire->needed . ", " . $fire->rolled . ", $gameid, '" . $c->real_escape_string($fire->notes ?? '') . "', " . $fire->shotshit . ", " . $fire->shots . ", '" . $c->real_escape_string($fire->pubnotes ?? '') . "', 0, '" . $c->real_escape_string($fire->x ?? '') . "', '" . $c->real_escape_string($fire->y ?? '') . "', '" . $c->real_escape_string($fire->damageclass ?? '') . "', '" . $c->real_escape_string($fire->resolutionOrder ?? '') . "')";

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
        // Discord turn notifications: record the flip in-memory, sent after commit
        // (DiscordNotifier::flush). class_exists guard so a stale autoload map
        // degrades to no-pings instead of a fatal.
        if (class_exists('DiscordNotifier')) DiscordNotifier::recordWaiting($gameid, $playerid, $waiting);
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
        if (class_exists('DiscordNotifier')) DiscordNotifier::recordWaiting($gameid, 'ALL', $waiting);
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

    // --- Discord turn notifications (DiscordNotifier / profile.php) ---

    // Returns discord_id (verified/bound), dm_channel_id, and the pending
    // verification fields. discord_verify_code is included — callers that hand
    // this to a page (Manager::getPlayerDiscordRow) must strip it first.
    public function getPlayerDiscordRow($playerid)
    {
        $row = null;
        if ($stmt = $this->connection->prepare(
            "SELECT discord_id, dm_channel_id, discord_verify_id, discord_verify_code, discord_verify_expires
               FROM player WHERE id = ?"
        )) {
            $stmt->bind_param('i', $playerid);
            $stmt->execute();
            $stmt->bind_result($discordId, $dmChannelId, $verifyId, $verifyCode, $verifyExpires);
            if ($stmt->fetch()) {
                $row = new stdClass();
                $row->discord_id = $discordId;
                $row->dm_channel_id = $dmChannelId;
                $row->discord_verify_id = $verifyId;
                $row->discord_verify_code = $verifyCode;
                $row->discord_verify_expires = $verifyExpires;
            }
            $stmt->close();
        }
        return $row;
    }

    // Store a pending challenge (does NOT touch discord_id — binding only happens
    // on successful verify). Overwrites any prior pending challenge.
    public function setPlayerDiscordVerification($playerid, $verifyId, $code, $expires)
    {
        if ($stmt = $this->connection->prepare(
            "UPDATE player
                SET discord_verify_id = ?, discord_verify_code = ?, discord_verify_expires = ?
              WHERE id = ?"
        )) {
            $stmt->bind_param('ssii', $verifyId, $code, $expires, $playerid);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Bind a verified Discord ID. The caller has proven ownership, so we transfer
    // the ID off any other account first (uniqueness recovery), then set it here
    // and clear the pending challenge. One transaction so the unique index never
    // transiently conflicts.
    public function bindVerifiedDiscordId($playerid, $discordId)
    {
        $this->startTransaction();
        try {
            if ($stmt = $this->connection->prepare(
                "UPDATE player SET discord_id = NULL, dm_channel_id = NULL
                  WHERE discord_id = ? AND id <> ?"
            )) {
                $stmt->bind_param('si', $discordId, $playerid);
                $stmt->execute();
                $stmt->close();
            }
            if ($stmt = $this->connection->prepare(
                "UPDATE player
                    SET discord_id = ?, dm_channel_id = NULL,
                        discord_verify_id = NULL, discord_verify_code = NULL, discord_verify_expires = NULL
                  WHERE id = ?"
            )) {
                $stmt->bind_param('si', $discordId, $playerid);
                $stmt->execute();
                $stmt->close();
            }
            $this->endTransaction(false, true);   // force commit even in testMode
        } catch (Exception $e) {
            $this->endTransaction(true);
            throw $e;
        }
    }

    // Clear only the pending challenge (keeps any existing verified binding).
    public function clearPlayerDiscordVerification($playerid)
    {
        if ($stmt = $this->connection->prepare(
            "UPDATE player
                SET discord_verify_id = NULL, discord_verify_code = NULL, discord_verify_expires = NULL
              WHERE id = ?"
        )) {
            $stmt->bind_param('i', $playerid);
            $stmt->execute();
            $stmt->close();
        }
    }

    // Full opt-out / reset: drop the binding, the DM cache and any pending challenge.
    public function clearPlayerDiscord($playerid)
    {
        if ($stmt = $this->connection->prepare(
            "UPDATE player
                SET discord_id = NULL, dm_channel_id = NULL,
                    discord_verify_id = NULL, discord_verify_code = NULL, discord_verify_expires = NULL
              WHERE id = ?"
        )) {
            $stmt->bind_param('i', $playerid);
            $stmt->execute();
            $stmt->close();
        }
    }

    public function setPlayerDmChannelId($playerid, $channelId)
    {
        if ($stmt = $this->connection->prepare(
            "UPDATE player SET dm_channel_id = ? WHERE id = ?"
        )) {
            $stmt->bind_param('si', $channelId, $playerid);
            $stmt->execute();
            $stmt->close();
        }
    }

    // playerid => row{discord_id, dm_channel_id} for every player in the game.
    public function getDiscordIdsInGame($gameid)
    {
        $players = array();
        if ($stmt = $this->connection->prepare(
            "SELECT DISTINCT p.id, p.discord_id, p.dm_channel_id
               FROM player p
               JOIN tac_playeringame pig ON pig.playerid = p.id
              WHERE pig.gameid = ?"
        )) {
            $stmt->bind_param('i', $gameid);
            $stmt->execute();
            $stmt->bind_result($id, $discordId, $dmChannelId);
            while ($stmt->fetch()) {
                $row = new stdClass();
                $row->discord_id = $discordId;
                $row->dm_channel_id = $dmChannelId;
                $players[$id] = $row;
            }
            $stmt->close();
        }
        return $players;
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
	
	public function deleteMovement($gameid, $shipid, $turn)
    {
        $sql = "DELETE FROM tac_shipmovement 
			WHERE gameid = $gameid and turn = $turn and shipid = $shipid and preturn <> 1 and type <> 'deploy'"; 
        $this->update($sql);
    }
	

    public function submitMovement($gameid, $shipid, $turn, $movements, $acceptPreturn = false)
    {
        try {

            foreach ($movements as $movement) {

                if ($movement->type == "start" || $movement->turn != $turn)
                    continue;

                //Transient forced JINKS (Gravitic Augmenter free jinks) are re-added in-memory
                //every load and must never be written to the DB, or they would accumulate.
                //Only jinks: forced pivots (pivotleft/right, rotateLeft/right) are real committed
                //moves that DO persist through this path.
                if ($movement->type == "jink" && !empty($movement->forced))
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

    /**
     * Games with recent activity, for the Recent Games window on games.php.
     *
     * Site-wide, not per-player (same as getFirePhaseGames, which took a $playerid and
     * then ignored it): the id is used only to flag which rows are the caller's, so the
     * window's MINE filter has something to work with. `players` is what makes the
     * name search possible.
     *
     * Deliberately plain rows — getFirePhaseGames constructed a full TacGamedata per row
     * and JSON-encoded it, shipping background/creator/description/points to render one
     * line of text.
     *
     * $days/$limit are ints from the caller, interpolated rather than bound: MariaDB is
     * fussy about placeholders in INTERVAL and LIMIT.
     */
    public function getRecentGames($playerid, $days = 14, $limit = 100)
    {
        $days  = max(1, (int)$days);
        $limit = max(1, (int)$limit);

        //turn > 0 skips games still in creation/lobby.
        $stmt = $this->connection->prepare("
            SELECT g.id, g.name, g.turn, g.status, g.slots, g.gamespace, g.rules,
                   COUNT(DISTINCT CASE WHEN p.playerid > 0 THEN p.playerid END) AS playerCount,
                   COUNT(CASE WHEN p.playerid > 0 THEN 1 END) AS occupiedSlots,
                   MAX(CASE WHEN p.playerid = ? THEN 1 ELSE 0 END) AS mine,
                   GROUP_CONCAT(DISTINCT pl.username ORDER BY pl.username SEPARATOR ', ') AS players,
                   MAX(p.lastactivity) AS lastActivity
            FROM tac_game g
            JOIN tac_playeringame p ON p.gameid = g.id
            LEFT JOIN player pl ON pl.id = p.playerid
            WHERE g.turn > 0
              AND p.lastactivity >= DATE_SUB(NOW(), INTERVAL $days DAY)
            GROUP BY g.id
            ORDER BY lastActivity DESC
            LIMIT $limit
        ");

        $games = [];

        if ($stmt) {
            $stmt->bind_param('i', $playerid);
            $stmt->bind_result($id, $gameName, $turn, $status, $slots, $gamespace, $rules,
                               $playerCount, $occupiedSlots, $mine, $players, $lastActivity);
            $stmt->execute();
            while ($stmt->fetch()) {
                $desc = $this->describeGameRules($rules, $gamespace);
                $games[] = [
                    "id"           => $id,
                    "name"         => $gameName,
                    "turn"         => $turn,
                    "status"       => $status,
                    "playerCount"  => $playerCount,
                    "slots"        => $slots,
                    "playerSlots"  => $this->playerSlots($slots, $occupiedSlots, $playerCount),
                    "map"          => $desc["map"],
                    "rules"        => $desc["rules"],
                    "ladder"       => $desc["ladder"],
                    "test"         => $desc["test"],
                    "mine"         => ((int)$mine === 1),
                    "players"      => ($players === null) ? '' : $players,
                    "lastActivity" => $lastActivity
                ];
            }
            $stmt->close();
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
        //Chameleon phantom sheets. Must follow onConstructed(): that is what applies enhancements,
        //and the disguise class IS an enhancement. It also sets the $chameleonPresent gate this
        //returns on immediately for every ordinary game.
        $this->getChameleonPhantoms($gamedata, $turn);


        return $gamedata;
    }


    /**
     * Decode a tac_game.rules blob into the flags + display chips the games lists need.
     *
     * Rule keys are only PRESENT when enabled (createGame.js deletes the disabled ones),
     * which is why the old strpos($rules, 'friendlyFire') checks happened to work — but
     * one server-side default writing false would have lit every chip. Decode instead.
     * Note `rules` is sometimes the JSON array '[]' rather than an object, and may be
     * NULL, so guard the decode.
     */
    /**
     * Denominator for the "N/M PLAYERS" readout, de-duplicated.
     *
     * tac_game.slots counts SEATS, not people, and one player may hold several seats of
     * the same game (4247: three slots, two of them the same player). The numerator has
     * always been COUNT(DISTINCT playerid), so a doubled-up player read as "2/3 players"
     * — advertising a third player who can never arrive. Subtract the doubled-up seats so
     * both sides of the slash count people: 2/2.
     *
     * Sent alongside the raw `slots`, not instead of it, so nothing that legitimately
     * wants the seat count silently gets a different number.
     *
     * Every game in the data has exactly one tac_playeringame row per slot, so
     * $occupiedSlots <= $slots holds; the clamps guard a malformed row rather than a
     * case seen in practice.
     */
    private function playerSlots($slots, $occupiedSlots, $playerCount) {
        $doubledUp = max(0, (int)$occupiedSlots - (int)$playerCount);
        return max((int)$playerCount, (int)$slots - $doubledUp);
    }

    private function describeGameRules($rulesJson, $gamespace) {
        $r = json_decode((string)$rulesJson, true);
        if (!is_array($r)) $r = array();

        $chips = array();
        $chips[] = !empty($r['initiativeCategories']) ? 'SIM MOV' : 'STD MOV';

        //Terrain: count the actual features rather than trusting the keys' presence — a
        //moons entry of {small:0,medium:0,large:0} is a game with no terrain in it, and
        //that shape is common in the live data.
        $asteroids = isset($r['asteroids']) ? (int)$r['asteroids'] : 0;
        $moons     = (isset($r['moons']) && is_array($r['moons'])) ? $r['moons'] : array();
        $moonCount = (int)($moons['small'] ?? 0) + (int)($moons['medium'] ?? 0) + (int)($moons['large'] ?? 0);
        if ($asteroids > 0 || $moonCount > 0) $chips[] = 'TERRAIN';

        if (!empty($r['allowMines']))   $chips[] = 'MINES';
        if (!empty($r['allowReinforcements'])) $chips[] = 'REINF';
        if (!empty($r['desperate']))    $chips[] = 'DESPERATE';
        if (!empty($r['friendlyFire'])) $chips[] = 'FRIENDLY FIRE';

        $space = (string)$gamespace;

        return array(
            "map"    => ($space === '' || $space === '-1x-1') ? 'OPEN' : $space,
            "rules"  => $chips,
            "ladder" => !empty($r['ladder']),
            "test"   => !empty($r['fleetTest'])
        );
    }

    public function getPlayerGames($playerid) {
        //One row per ACTIVE game this player is still in.
        //Returns STRUCTURED fields: presentation (the ladder tag, the rule chips) is the
        //client's job. This used to bake inline-styled HTML into `name`, which is why the
        //cards could not be restyled without editing SQL result assembly.
        //playerCount is new — the client previously had no count for active games.
        $stmt = $this->connection->prepare("
            SELECT g.id, g.name, pg.waiting, g.gamespace, g.rules, g.slots, g.turn,
                   (SELECT COUNT(DISTINCT playerid) FROM tac_playeringame
                     WHERE gameid = g.id AND playerid > 0) AS playerCount,
                   (SELECT COUNT(*) FROM tac_playeringame
                     WHERE gameid = g.id AND playerid > 0) AS occupiedSlots
            FROM tac_playeringame pg
            JOIN tac_game g ON pg.gameid = g.id
            WHERE g.status = 'ACTIVE'
            AND pg.playerid = ?
            AND pg.surrendered IS NULL
        ");

        /* Keyed by game id while fetching, because the query returns one row per SLOT this
           player holds: someone sitting in two slots of the same game got two identical
           cards. (The old renderer hid it — it only appended a card that was not already in
           the DOM. The current one re-renders from state, so both rows showed.)

           `waiting` merges toward 0: it means "this player still owes an order", so if ANY
           of their slots owes one the game is waiting on them and keeps the highlight. */
        $games = [];

        if ($stmt) {
            $stmt->bind_param('i', $playerid);
            $stmt->bind_result($id, $gameName, $waiting, $gamespace, $rules, $slots, $turn, $playerCount, $occupiedSlots);
            $stmt->execute();
            while ($stmt->fetch()) {
                if (isset($games[$id])) {
                    if ((int)$waiting === 0) $games[$id]["waiting"] = 0;
                    continue;
                }

                $desc = $this->describeGameRules($rules, $gamespace);
                $games[$id] = [
                    "id"          => $id,
                    "name"        => $gameName,
                    "waiting"     => $waiting,
                    "status"      => "ACTIVE",
                    "turn"        => $turn,
                    "playerCount" => $playerCount,
                    "slots"       => $slots,
                    "playerSlots" => $this->playerSlots($slots, $occupiedSlots, $playerCount),
                    "map"         => $desc["map"],
                    "rules"       => $desc["rules"],
                    "ladder"      => $desc["ladder"],
                    "test"        => $desc["test"]
                ];
            }
            $stmt->close();
        }

        //back to a list: keyed by game id it would json_encode as an OBJECT, and the
        //client iterates this as an array
        $games = array_values($games);

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
        //Games waiting in the lobby. Query unchanged; only the row assembly changed —
        //it used to build '<span style=...>LADDER: </span>' + name + '<br><span
        //class="gameRules">(...)</span>' and replace the name outright with
        //'Fleet Builder'. All three are presentation and now travel as flags.
		$stmt = $this->connection->prepare("select g.id as parentGameId, g.name, g.slots, g.gamespace, g.rules, (select count(distinct playerid) from tac_playeringame where gameid = parentGameId and playerid > 0 ) as numberOfPlayers, (select count(*) from tac_playeringame where gameid = parentGameId and playerid > 0 ) as occupiedSlots, (SELECT count(*) FROM tac_playeringame WHERE gameid = g.id AND playerid = ?) as userInGame from tac_game g WHERE  g.status = 'LOBBY';");

        $games = [];

        if ($stmt) {
            $stmt->bind_param("i", $userid);
            $stmt->bind_result($id, $gameName, $slots, $gamespace, $rules, $playerCount, $occupiedSlots, $userInGame);
            $stmt->execute();
            while ($stmt->fetch()) {
                $desc = $this->describeGameRules($rules, $gamespace);

                // FILTER: If it's a Fleet Test game, and user is not in it, SKIP.
                if ($desc["test"] && $userInGame == 0) {
                    continue;
                }

                $games[] = [
                    "id"          => $id,
                    "name"        => $gameName,
                    "status"      => "LOBBY",
                    "playerCount" => $playerCount,
                    "slots"       => $slots,
                    "playerSlots" => $this->playerSlots($slots, $occupiedSlots, $playerCount),
                    "map"         => $desc["map"],
                    "rules"       => $desc["rules"],
                    "ladder"      => $desc["ladder"],
                    "test"        => $desc["test"]
                ];
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
                id, playerid, name, phpclass, slot, enhvalue, reinforcement, arrivalturn, arrivalvia
            FROM
                tac_ship
            WHERE
                id = ?
            "
        );

        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->bind_result($id, $playerid, $name, $phpclass, $slot, $enhvalue, $reinforcement, $arrivalturn, $arrivalvia);
            $stmt->execute();
            while ($stmt->fetch()) {
                $ship = new $phpclass($id, $playerid, $name, $slot);
				//(float): mysqli hands a DECIMAL back as a STRING, and this value is
				//json_encoded to the client, where `pointCost + pointCostEnh` would then
				//CONCATENATE instead of adding. See db/fractionalEnhancementValue.sql.
				$ship->pointCostEnh = (float)$enhvalue;
				$this->applyReinforcementFields($ship, $reinforcement, $arrivalturn, $arrivalvia);
            }
            $stmt->close();
        }

        return $ship;
    }

	/* REINFORCEMENTS_PLAN.md §3.1 — the three tac_ship reinforcement columns, cast once for both
	   ship readers. Casting matters on all three: mysqli hands back STRINGS, and getTurnDeployed
	   tests `$this->arrivalTurn === null` with a STRICT ===, so a "0"/"" would read as an arrival
	   on turn 0 rather than as "still in hyperspace". NULL must survive as null and nothing else. */
	private function applyReinforcementFields($ship, $reinforcement, $arrivalturn, $arrivalvia)
	{
		$ship->reinforcement = (bool)$reinforcement;
		$ship->arrivalTurn   = ($arrivalturn === null) ? null : (int)$arrivalturn;
		$ship->arrivalVia    = ($arrivalvia  === null) ? null : (int)$arrivalvia;
	}

    public function getTacShips($gamedata, $turn, $allData = true)
    {

        //$starttime = time();
        $ships = array();

        $stmt = $this->connection->prepare(
            "SELECT
                id, playerid, name, phpclass, slot, enhvalue, reinforcement, arrivalturn, arrivalvia
            FROM
                tac_ship
            WHERE
                tacgameid = ?
            "
        );

        if ($stmt) {
            $stmt->bind_param('i', $gamedata->id);
            $stmt->bind_result($id, $playerid, $name, $phpclass, $slot, $enhvalue, $reinforcement, $arrivalturn, $arrivalvia);
            $stmt->execute();
            while ($stmt->fetch()) {
                $ship = new $phpclass($id, $playerid, $name, $slot);
				//(float): mysqli hands a DECIMAL back as a STRING, and this value is
				//json_encoded to the client, where `pointCost + pointCostEnh` would then
				//CONCATENATE instead of adding. See db/fractionalEnhancementValue.sql.
				$ship->pointCostEnh = (float)$enhvalue;
				$this->applyReinforcementFields($ship, $reinforcement, $arrivalturn, $arrivalvia);
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
            //Enhancements are read here, ABOVE the criticals/damage queries, because an enhancement
            //may MOUNT SYSTEMS (Extra Tendrils) and those queries silently drop any row whose
            //systemid does not resolve - see getEnhancementsForShips. After getFlightSize, because
            //FighterFlight::populate() rebuilds a flight's systems from scratch.
            $this->getEnhancementsForShips($gamedata);
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
                gameid = ? AND (turn = 1 OR turn = ? OR turn = ? OR type = 'deploy' OR type = 'start') 
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


    /*tac_enhancements -> $ship->enhancementOptions, plus any systems an enhancement MOUNTS.
      Called from getTacShips deliberately EARLY - before getCriticalsForShips/getDamageForShips.

      Those two resolve every row through $ship->getSystemById() and skip, without complaint, any
      row that does not resolve. So a system an enhancement creates has to already exist when they
      run, or it silently loses its damage and criticals on every load - and for Extra Tendrils that
      is not cosmetic: a tendril stores its absorbed energy AS damage, so it would come back empty
      after every page refresh.

      Only mounting happens here. APPLYING the enhancements (stat changes) stays where it was, in
      BaseShip::onConstructed, which runs after all of this.*/
    private function getEnhancementsForShips($gamedata)
    {
        $allEnhancements = $this->getEnhancementsForGame($gamedata->id);
        //Per-system enhancements (WEAPON_ENHANCEMENTS_PLAN.md §4.6). ONE query for the whole
        //game, and read at the same EARLY point as the ship-level rows, for the same reason:
        //everything downstream resolves systems through getSystemById and skips, silently, any
        //row that does not resolve.
        $allSystemEnhancements = $this->getSystemEnhancementsForGame($gamedata->id);

        foreach ($gamedata->ships as $ship){
            $shipEnhancements = isset($allEnhancements[$ship->id]) ? $allEnhancements[$ship->id] : array();

            if( count($shipEnhancements) == 0 ){ //no enhancements! add empty one just to show it's been read
                $ship->enhancementOptions[] = array('NONE','-', 0,0,0,0); //[ID,readableName,numberTaken,limit,price,priceStep]
            }
            foreach($shipEnhancements as $entry){
                $ship->enhancementOptions[] = array($entry[0],$entry[2], $entry[1],0,0,0);
            }

            Enhancements::addEnhancementSystems($ship);

            /* Rebuilt into the PURCHASE tuple shape (see BaseShip::$systemEnhancements):
                 [enhID, label, count, limit, TOTAL PAID, priceStep, systemid, sysname]
               limit and priceStep are 0 in game - nothing can be bought here, so there is
               nothing for them to constrain, exactly as the ship-level rebuild above zeroes
               its price columns. enhvalue is DECIMAL and mysqli hands it back as a STRING;
               cast it, or pointCostSysEnh becomes a string and the fleet total concatenates.

               ⭐ VALIDATED, not merely rebuilt - the D13 integrity check, the same rule
               Enhancements::setSystemEnhancements applies before it changes a stat and
               loadSavedFleet applies before it re-prices. A stored systemid is POSITIONAL, so a
               row whose id no longer resolves, or resolves to a system of a different NAME, is
               describing somebody else's ship and is dropped here rather than carried.

               Without this the row still reached the client and still cost points even though the
               server refused to apply it: phantom refit badges on an Omega's heavy lasers and +52
               points on a fleet that had bought nothing (game 4302, 2026-08-17). The cause there
               was orphaned rows plus a recycled tac_ship.id - fixed at source in leaveSlot() - but
               a contributor revising a hull shifts every id after the system they insert, which is
               the routine way for the same mismatch to appear.

               AFTER addEnhancementSystems because an enhancement may MOUNT a system (Extra
               Tendrils) and a refit may have been bought on one; validating first would drop it. */
            if (isset($allSystemEnhancements[$ship->id])) {
                foreach ($allSystemEnhancements[$ship->id] as $entry) {
                    // entry: [systemid, sysname, enhid, numbertaken, enhname, enhvalue]
                    $system = $ship->getSystemById((int)$entry[0]);
                    if (!$system) continue;                              //stale id - drop, never guess
                    //Older rows may carry no name; those fall through on the id alone.
                    if ($entry[1] !== '' && (string)$system->name !== (string)$entry[1]) continue;

                    $ship->systemEnhancements[] = array(
                        $entry[2], $entry[4], (int)$entry[3], 0, (float)$entry[5], 0, (int)$entry[0], $entry[1]
                    );
                    $ship->pointCostSysEnh += (float)$entry[5];
                }
            }
        }
    } //endof function getEnhancementsForShips


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
				$targetShip = $gamedata->getShipById($shipid);
				if ($targetShip === null) continue; //shipid not in this gamedata (eg. Chameleon phantom sheets, which use negative ids) - not an error, just not ours to load here
				$targetSystem = $targetShip->getSystemById($systemid);
				if ($targetSystem === null) continue;
                $targetSystem->setDamage(
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
					if ($param && ($param[0] == '{' || $param[0] == '[')) {
						$decoded = json_decode($param, true);
						if (is_array($decoded)) {
							$param = $decoded;
						}
					}

					$targetShip = $gamedata->getShipById($shipid);
					if ($targetShip === null) continue; //shipid not in this gamedata (eg. Chameleon phantom sheets, which use negative ids)
					$targetSystem = $targetShip->getSystemById($systemid);
					if ($targetSystem === null) continue;

					//Defence in depth: the line below is `new $type(...)` on a string read
					//straight out of the database, so a bad row would be arbitrary class
					//instantiation on every load of this game. Every write path validates
					//before storing (see PreBattleDamage::isValidCriticalType); this guards
					//rows that are already there.
					if (!class_exists($type) || !is_subclass_of($type, 'Critical')) continue;

					$crit = new $type($id, $shipid, $systemid, $type, $turn, $turnEnd);
					$crit->param = $param;
					$targetSystem->setCritical(
						$crit,
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

    /*Chameleon Sensor Suite, Stage 4 - build every disguised ship's phantom sheet and fill it from
      the negative-shipid rows in tac_damage / tac_critical (D1, D2).

      Runs from getTacGamedata AFTER $gamedata->onConstructed(), which is the earliest point the
      disguise class is known: it arrives as an ENHANCEMENT, and enhancements are applied by
      BaseShip::onConstructed(). (The plan said "end of getSystemDataForShips" - that is too early,
      because that method only loads the raw tac_enhancements rows, it does not apply them.)

      Behind the per-load $chameleonPresent gate, so a game with no disguised ship runs one boolean
      test and issues no extra queries at all.

      NOTE on power: D12 called for synthesising a power entry per phantom system so the hull does
      not look unpowered. Verified false and deliberately NOT done - tac_power holds only EXCEPTION
      records (1 offline, 2 boost, 3 overload), so no entries is the normal healthy state that every
      real ship on the board also has. Synthesising anything here would make the phantom the odd one
      out, which is the opposite of the intent.*/
    private function getChameleonPhantoms(TacGamedata $gamedata, $fetchTurn)
    {
        if (!TacGamedata::$chameleonPresent) return;

        $phantoms = array(); //keyed by the NEGATIVE id, which is what the rows carry
        $owners = array();
        foreach ($gamedata->ships as $ship) {
            $phantom = $ship->buildChameleonPhantom();
            if ($phantom === null) continue;
            $phantoms[$phantom->id] = $phantom;
            $owners[] = $ship;
        }

        if (empty($phantoms)) return;

        //Same order as the real ships get (criticals, then damage) so a system that is both
        //critted and destroyed resolves identically on both sheets.
        $this->getChameleonPhantomCriticals($gamedata, $phantoms, $fetchTurn);
        $this->getChameleonPhantomDamage($gamedata, $phantoms, $fetchTurn);

        //And only THEN construct the systems - onConstructed latches $destroyed off the damage
        //list, so running it any earlier serves a fatally damaged phantom system as intact.
        foreach ($owners as $ship) {
            $ship->finaliseChameleonPhantom($gamedata->turn, $gamedata->phase);
        }
    }

    private function getChameleonPhantomDamage(TacGamedata $gamedata, $phantoms, $fetchTurn)
    {
        $stmt = $this->connection->prepare(
            "SELECT
                id, shipid, gameid, turn, systemid, damage, armour, shields, fireorderid, destroyed, undestroyed, pubnotes, damageclass
            FROM
                tac_damage
            WHERE
                gameid = ? AND turn <= ? AND shipid < 0
            ORDER BY
                id ASC
            " //id order guarantees destroyed/undestroyed entries resolve in sequence, as for real ships
        );

        if (!$stmt) return;

        $stmt->bind_param('ii', $gamedata->id, $fetchTurn);
        $stmt->bind_result($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $undestroyed, $pubnotes, $damageclass);
        $stmt->execute();
        while ($stmt->fetch()) {
            if (!isset($phantoms[$shipid])) continue;   //a phantom for a ship not in this game, or no longer disguised
            $system = $phantoms[$shipid]->getSystemById($systemid);
            if ($system === null) continue;             //simulacrum changed since the row was written
            $system->setDamage(
                new DamageEntry($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $undestroyed, $pubnotes, $damageclass)
            );
        }
        $stmt->close();
    }

    private function getChameleonPhantomCriticals(TacGamedata $gamedata, $phantoms, $fetchTurn)
    {
        /*Phantoms take damage but roll no criticals of their own (D3c) - Criticals::setCriticals
          walks $gamedata->ships, which they are deliberately not in. This loader exists anyway so
          that turning D3c on later is a resolution change only, with the read path already proven,
          and so a hand-written row behaves. Mirrors getCriticalsForShips, including its
          forced-offline carry-back from the previous turn.*/
        $stmt = $this->connection->prepare(
            "SELECT
                id, shipid, systemid, type, turn, turnend, param
            FROM
                tac_critical
            WHERE
                gameid = ? AND turn <= ? AND (turnend = 0 OR turnend >= ?) AND shipid < 0
            "
        );

        if (!$stmt) return;

        $turnEnd = 0;
        $turnBefore = $fetchTurn - 1;
        $stmt->bind_param('iii', $gamedata->id, $fetchTurn, $turnBefore);
        $stmt->bind_result($id, $shipid, $systemid, $type, $turn, $turnEnd, $param);
        $stmt->execute();
        while ($stmt->fetch()) {
            $doAddCrit = false;
            if (($turnEnd == 0) || ($turnEnd >= $fetchTurn)) $doAddCrit = true;
            if (($type == 'ForcedOfflineOneTurn') || ($type == 'ForcedOfflineForTurns')) $doAddCrit = true;
            if (!$doAddCrit) continue;

            if (!isset($phantoms[$shipid])) continue;
            if (!class_exists($type)) continue;         //never construct an arbitrary class from a DB string
            $system = $phantoms[$shipid]->getSystemById($systemid);
            if ($system === null) continue;

            if ($param && ($param[0] == '{' || $param[0] == '[')) {
                $decoded = json_decode($param, true);
                if (is_array($decoded)) $param = $decoded;
            }

            $crit = new $type($id, $shipid, $systemid, $type, $turn, $turnEnd);
            $crit->param = $param;
            $system->setCritical($crit, $gamedata->turn);
        }
        $stmt->close();
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


		//Enhancement info used to be read HERE. It moved UP, to getEnhancementsForShips, called
		//from getTacShips before the criticals/damage queries - an enhancement may mount systems
		//and those systems have to exist before their rows are read. See that method.
		//get enhancement info - optimization: single query for all ships
		/*
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
        */

		//get individual notes for systems - optimization: single query
        $allNotes = $this->getIndividualNotesForGame($gamedata, $fetchTurn);

		//Clear the Gravitic Augmenter's per-LOAD dedup guards before the onIndividualNotesLoaded sweep.
		//They dedup multiple Augmenters buffing/shifting the same target within THIS load, but must not
		//survive into a later load in the same request (one request loads gamedata more than once, e.g.
		//advanceGameState then FireGamePhase::advance) - otherwise the second load skips the buff and a
		//shot at the Warrior resolves without its jink. Guarded so it's a no-op if the class isn't loaded.
		if (class_exists('GraviticAugmenter')) GraviticAugmenter::resetPerLoadState();

		//Walkers of Sigma-957 (WALKERS_OF_SIGMA_PLAN.md 3.4): the Chromatic Pulse Driver's
		//fleet-wide shield adaptation is rebuilt from scratch out of the CPDSCAN notes replayed by
		//the sweep below. Same reason as the line above - one request loads gamedata more than once
		//(advanceGameState, then the phase's advance()), and without this reset the second load
		//would count every fleet's adaptation twice.
		//⚠️ class_exists WITH AUTOLOADING OFF, deliberately. The gate this feature hangs off is
		//TacGamedata::$cpdAdaptationPresent, and the whole point is that a game with no CPD scan in
		//it never loads the registry at all: if the class is not loaded its static table is empty
		//by definition, so there is nothing to reset. Turning autoloading back on here would pull
		//the file into every gamedata load in the game.
		TacGamedata::$cpdAdaptationPresent = false;
		if (class_exists('CpdScanRegistry', false)) CpdScanRegistry::resetPerLoadState();

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

    /* Every PER-SYSTEM enhancement row in the game, as shipid => [[systemid, sysname,
       enhid, numbertaken, enhname, enhvalue], …]. ONE query for the whole game, exactly
       like getEnhancementsForGame above - this runs on every game.php load.
       ⚠️ enhvalue is DECIMAL and mysqli hands it back as a PHP STRING; cast at the point of
       use (arch_fractional_enhancement_value). */
    private function getSystemEnhancementsForGame($gameID){
        $toReturn = array(); // Map shipid -> array of entries
        $stmt = $this->connection->prepare(
            "SELECT
                shipid, systemid, sysname, enhid, numbertaken, enhname, enhvalue
            FROM
                tac_sys_enhancements
            WHERE
                gameid = ?
            "
        );
        if ($stmt)
        {
            $stmt->bind_param('i', $gameID);
            $stmt->bind_result($shipID, $systemid, $sysname, $enhID, $numbertaken, $enhname, $enhvalue);
            $stmt->execute();
            while ($stmt->fetch())
            {
                if (!isset($toReturn[$shipID])) $toReturn[$shipID] = array();
                $toReturn[$shipID][] = array($systemid, $sysname, $enhID, $numbertaken, $enhname, $enhvalue);
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

                //Corrupt fire order guard. A fire order can reference a
                //(shooterid, weaponid) that no longer resolves to a real weapon
                //on reload. Confirmed live cause: a client submitting firing with
                //a STALE staticShips blueprint — the posted weaponids belong to an
                //older system layout for that phpclass, so on reload they either
                //miss entirely or land on a non-weapon system. System ids are
                //positional (BaseShip::addSystem assigns id = current system
                //count), so any change to a ship's system list shifts every later
                //id and desynchronises older client blueprints.
                //
                //Three failure shapes, all funnelled through this one chokepoint
                //so no downstream consumer ever sees a bad attach (e.g. the
                //unguarded $weapon->ballistic at TacGamedata::onConstructed only
                //iterates orders that were actually attached here):
                //  1. shooter ship missing            -> getShipById() null
                //  2. weapon id missing on shooter     -> getSystemById() null
                //  3. id resolves to a NON-weapon      -> e.g. Thruster, which has
                //     no ->ballistic and threw "Undefined property: Thruster::$ballistic"
                //
                //A skipped order is a shot dropped from the replay/combat log, but
                //that order was never valid for this ship to begin with. The real
                //fix is upstream: validate posted weaponids against the server's
                //construction at firing-submit time, and/or version the client
                //blueprint so stale submissions are rejected before they hit the DB.
                $shooter = $gamedata->getShipById($shooterid);
                if ($shooter === null) {
                    Debug::log("getFireOrdersForShips: corrupt fire order $id "
                        . "(game {$gamedata->id}, turn $turn) — shooter $shooterid "
                        . "not found; skipping. weaponid=$weaponid type=$type");
                    continue;
                }
                $weapon = $shooter->getSystemById($weaponid);
                if ($weapon === null) {
                    Debug::log("getFireOrdersForShips: corrupt fire order $id "
                        . "(game {$gamedata->id}, turn $turn) — weapon $weaponid "
                        . "not found on shooter $shooterid ('{$shooter->name}'); "
                        . "skipping. type=$type targetid=$targetid");
                    continue;
                }
                if (!($weapon instanceof Weapon)) {
                    Debug::log("getFireOrdersForShips: corrupt fire order $id "
                        . "(game {$gamedata->id}, turn $turn) — system $weaponid on "
                        . "shooter $shooterid ('{$shooter->name}') is a "
                        . get_class($weapon) . ", not a Weapon (stale client "
                        . "blueprint?); skipping. type=$type targetid=$targetid");
                    continue;
                }

                $weapon->setFireOrder($entry);
            }
            $stmt->close();
        }
    }

    public function submitSingleFireorder($gameid, $fireOrder)
    {
            $c = $this->connection;
            $sql = "INSERT INTO `tac_fireorder` VALUES (null, '" . $c->real_escape_string($fireOrder->type ?? '') . "', " . $fireOrder->shooterid . ", " . $fireOrder->targetid . ", " . $fireOrder->weaponid . ", " . $fireOrder->calledid . ", " . $fireOrder->turn . ", "
                . $fireOrder->firingMode . ", " . $fireOrder->needed . ", " . $fireOrder->rolled . ", $gameid, '" . $c->real_escape_string($fireOrder->notes ?? '') . "', " . $fireOrder->shotshit . ", " . $fireOrder->shots . ", '" . $c->real_escape_string($fireOrder->pubnotes ?? '') . "', 0, '" . $c->real_escape_string($fireOrder->x ?? '') . "', '" . $c->real_escape_string($fireOrder->y ?? '') . "', '" . $c->real_escape_string($fireOrder->damageclass ?? '') . "', '" . $c->real_escape_string($fireOrder->resolutionOrder ?? '') . "')";

            $this->update($sql);

            return mysqli_insert_id($this->connection);
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

    /* ============== LEGACY AUTH (pre 2026-07-10) — kept for emergency rollback ==============
       Replaced by the password_hash() implementations below. Old storage format was MySQL
       PASSWORD() ('*' + 40 hex chars); new format is bcrypt ($2y$...).
       ⚠️ ROLLBACK WARNING: the new code transparently rewrites a player's hash to bcrypt on
       their first successful login. This legacy code CANNOT verify bcrypt hashes — so simply
       uncommenting it after the new code has been live will LOCK OUT every account that logged
       in since the deploy. A real rollback needs this code AND the pre-deploy player-table
       backup restored together.

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
            // Step 1: Check if the username exists
            if ($stmt = $this->connection->prepare(
                "SELECT id FROM player WHERE username = ?")) {

                $stmt->bind_param('s', $username);
                $stmt->execute();
                $stmt->bind_result($id);
                $stmt->fetch();
                $stmt->close();
            }

            if (!$id)
                return 'USER_NOT_FOUND';

            // Step 2: Verify the password
            $id = false;
            if ($stmt = $this->connection->prepare(
                "SELECT id, accesslevel FROM player WHERE username = ? AND password = password(?)")) {

                $stmt->bind_param('ss', $username, $password);
                $stmt->execute();
                $stmt->bind_result($id, $access);
                $stmt->fetch();
                $stmt->close();
            }

            if (!$id)
                return 'WRONG_PASSWORD';

        } catch (Exception $e) {
            throw $e;
        }

        return array('id' => $id, 'access' => $access);
    }
    ============================== END LEGACY AUTH ============================== */


    public function registerPlayer($username, $password)
    {
        $username = htmlspecialchars($username);
        $username = $this->DBEscape($username);

        $exists = false;
        if ($stmt = $this->connection->prepare(
            "SELECT id FROM player WHERE username = ?")) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->bind_result($id);
            if ($stmt->fetch())
                $exists = true;
            $stmt->close();
        }
        if ($exists) {
            return false;
        }

        //Name the columns explicitly. The player table gains columns over time
        //(discord_id and friends), and a column-less INSERT ... VALUES fails at
        //prepare() time with "Column count doesn't match value count" as soon as
        //one is added.
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $inserted = false;
        if ($stmt = $this->connection->prepare("
            INSERT INTO
                player
                (username, password, accesslevel)
            VALUES
            (
                ?,
                ?,
                1
            );
            ")) {
            $stmt->bind_param('ss', $username, $hash);
            $inserted = $stmt->execute();
            $stmt->close();
        }

        //Never report success for an insert that did not happen: DBManager runs
        //with mysqli_report(MYSQLI_REPORT_ERROR) only, so DB errors surface as
        //warnings rather than exceptions and would otherwise be swallowed here —
        //leaving the caller to log in an account that was never created.
        if (!$inserted)
            throw new Exception("DBManager:registerPlayer, insert failed: " . $this->connection->error);

        return true;
    }


    public function changePassword($username, $passwordold, $passwordnew) //change password for a given account
    {
        $row = $this->getPlayerAuthRow($username);
        if (!$row || !$this->verifyPlayerPassword($passwordold, $row['hash'])) {
            return false;
        }

        $this->storePlayerPassword($row['id'], $passwordnew);

        return true;
    }


    public function authenticatePlayer($username, $password)
    {
        try {
            $row = $this->getPlayerAuthRow($username);

            if (!$row)
                return 'USER_NOT_FOUND';

            if (!$this->verifyPlayerPassword($password, $row['hash']))
                return 'WRONG_PASSWORD';

            //Transparent upgrade: rewrite legacy MySQL PASSWORD() hashes (and any
            //outdated password_hash format) using the raw password on successful login.
            if (password_needs_rehash($row['hash'], PASSWORD_DEFAULT))
                $this->storePlayerPassword($row['id'], $password);

        } catch (Exception $e) {
            throw $e;
        }

        return array('id' => $row['id'], 'access' => $row['access']);
    }


    private function getPlayerAuthRow($username)
    {
        $row = null;
        if ($stmt = $this->connection->prepare(
            "SELECT id, accesslevel, password FROM player WHERE username = ?")) {

            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->bind_result($id, $access, $hash);
            if ($stmt->fetch())
                $row = array('id' => $id, 'access' => $access, 'hash' => $hash);
            $stmt->close();
        }
        return $row;
    }


    //Emulates MySQL/MariaDB PASSWORD() — the pre-2026 storage format ('*' + 40 hex chars).
    private function legacyMysqlPassword($password)
    {
        return '*' . strtoupper(sha1(sha1($password, true)));
    }


    //Check a password against the stored hash: password_hash format first, then the
    //legacy MySQL PASSWORD() format. Legacy hashes were created from inconsistently
    //transformed input (registration hashed DBEscape(htmlspecialchars($pass)), password
    //change hashed htmlspecialchars($pass), login checked the raw string), so all three
    //variants are accepted — this also rescues accounts whose special-character
    //passwords could never match at login.
    private function verifyPlayerPassword($password, $storedHash)
    {
        if (password_verify($password, $storedHash))
            return true;

        $legacyCandidates = array(
            $password,
            htmlspecialchars($password),
            $this->DBEscape(htmlspecialchars($password)),
        );
        foreach ($legacyCandidates as $candidate) {
            if (hash_equals($storedHash, $this->legacyMysqlPassword($candidate)))
                return true;
        }

        return false;
    }


    private function storePlayerPassword($playerid, $password)
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($stmt = $this->connection->prepare(
            "UPDATE player SET password = ? WHERE id = ?")) {

            $stmt->bind_param('si', $hash, $playerid);
            $stmt->execute();
            $stmt->close();
        }
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

			//per-system enhancements (WEAPON_ENHANCEMENTS_PLAN.md §3.3). tac_sys_enhancements
			//has no FK to tac_game, same as tac_enhancements, so it needs its own DELETE.
            $stmt = $this->connection->prepare(
                "DELETE FROM
                    tac_sys_enhancements
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

    /**
     * Highest chat id that actually exists for a game, or 0 if it has none.
     *
     * Exists so ChatManager never caches a fast-poll watermark taken from the
     * client's `lastid`, which is a claim rather than a fact. See the long comment at
     * ChatManager::getChatMessages' empty-result branch for why an inflated watermark
     * is harmful (it switches the DB-sparing fast path OFF for a whole game chat).
     *
     * Cheap enough to be worth the round trip: it runs only on a fast-poll MISS, on
     * which a query has already been paid for anyway, and it is a bounded index
     * lookup -- MAX() on the leading column of the `gameid_id` index is resolved from
     * the index alone, and 3-day retention keeps the table tiny regardless.
     */
    public function getMaxChatMessageId($gameid = 0)
    {
        $maxId = 0;

        $stmt = $this->connection->prepare("
            SELECT
                MAX(id)
            FROM
                chat
            WHERE
                gameid = ?
        ");

        if ($stmt) {
            $stmt->bind_param('i', $gameid);
            // MAX() over no rows is NULL, not 0 — a game whose chat is empty (or whose
            // messages retention has just purged) lands here, and (int)null is 0.
            $stmt->bind_result($dbMax);
            $stmt->execute();
            $stmt->fetch();
            $stmt->close();
            $maxId = (int)$dbMax;
        }

        return $maxId;
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
        /* The cutoff is computed on the RIGHT of the comparison, never by wrapping the
           column. This used to read `DATE_ADD(time, INTERVAL 3 DAY) < NOW()`, which
           selects exactly the same rows but is not sargable: a column inside a function
           cannot be matched against an index, so no index on `time` could ever be used
           however the table was defined.

           Honesty about scale, so this is not mistaken for a hot fix: on live the 3-day
           retention actually works and `chat` holds only about a dozen rows, so today
           this predicate costs nothing either way. It was rewritten while chasing the
           2026-09-01 "Too many connections" bursts on a theory — MyISAM table locks —
           that turned out not to apply, because live `chat` has been InnoDB all along.
           See db/chatTableIndexes.sql for that write-up.

           It stays rewritten because a non-sargable predicate is a latent defect
           regardless of the current row count: it silently cannot use an index, so the
           cost is invisible right up until retention is lengthened or the purge stops
           running. */
        $stmt = $this->connection->prepare("
            DELETE FROM
                chat
            WHERE
                time < NOW() - INTERVAL 3 DAY
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
        // Every player in tac_ladder_rankings appears in standings; wins/losses
        // are counted directly from tac_ladder_games (no tac_game dependency),
        // so a player's score persists after their old games are purged.
        $sql = "SELECT r.playerid, r.rating, p.username,
                (SELECT COUNT(*) FROM tac_ladder_games g WHERE g.playerid = r.playerid AND g.status = 'WIN') as wins,
                (SELECT COUNT(*) FROM tac_ladder_games g WHERE g.playerid = r.playerid AND g.status = 'LOSS') as losses
                FROM tac_ladder_rankings r
                LEFT JOIN player p ON r.playerid = p.id
                ORDER BY r.rating DESC";

        return $this->query($sql);
    }
}

