<?php

	class ShipLoader{
	
		private static $factionDirMap = null;
		
		/**
		 * Build a mapping of faction names to directories containing their ships.
		 * Cached for 1 hour. Handles edge cases where ships might be in wrong directories.
		 */
		public static function getFactionDirMap(){
			if (self::$factionDirMap !== null) return self::$factionDirMap;
			
			$cacheDir = sys_get_temp_dir() . '/fv_cache';
			if (!is_dir($cacheDir)) {
				@mkdir($cacheDir, 0755, true);
			}
			
			$cacheFile = $cacheDir . '/faction_dir_map.cache';
			$cacheMaxAge = 3600; // 1 hour
			
			// Check for cached mapping (skip if debug mode or local server)
			if (php_sapi_name() === 'cli') {
				$isLocal = true; // or false, depending on your intention
			} else {
				$serverName = $_SERVER['SERVER_NAME'] ?? '';
				$isLocal = in_array($serverName, ['localhost', '127.0.0.1']);
			}
			if (!$isLocal && file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheMaxAge) {
				$cached = @unserialize(file_get_contents($cacheFile));
				if ($cached !== false) {
					self::$factionDirMap = $cached;
				
					return self::$factionDirMap;
				}
			}
			
			// Build mapping by scanning all ships
			$dir = dirname(__DIR__) . "/model/ships/";
			$map = [];
			
			$handle = opendir($dir);
			while ($handle !== false && false !== ($subdir = readdir($handle))) {
				if ($subdir[0] === '.' || !is_dir($dir . $subdir)) continue;
				
				$handle2 = opendir($dir . $subdir);
				while ($handle2 !== false && false !== ($file = readdir($handle2))) {
					if ($file === '.' || $file === '..' || substr($file, -4) !== '.php') continue;
					
					$className = substr($file, 0, -4);
					if (class_exists($className)) {
						try {
							$ship = new $className(1, 0, "", 0, 0, false, false, []);
							$faction = $ship->faction;
							if (!isset($map[$faction])) {
								$map[$faction] = [];
							}
							if (!in_array($subdir, $map[$faction])) {
								$map[$faction][] = $subdir;
							}
						} catch (Exception $e) {
							// Skip problematic ship classes
						}
					}
				}
				if ($handle2 !== false) closedir($handle2);
			}
			if ($handle !== false) closedir($handle);
			
			// Cache the mapping
			@file_put_contents($cacheFile, serialize($map), LOCK_EX);
			self::$factionDirMap = $map;
			return $map;
		}
		
		/**
		 * Get ship class names, optionally filtered by faction for performance.
		 */
		public static function getShipClassnames($faction = null){
			$dir = dirname(__DIR__) . "/model/ships/";
			$list = [];
			
			// If faction specified, only scan relevant directories
			$dirsToScan = null;
			if ($faction) {
				$map = self::getFactionDirMap();
				if (isset($map[$faction])) {
					$dirsToScan = $map[$faction];
				}
			}
			
			$handle = opendir($dir);
			while ($handle !== false && false !== ($entry = readdir($handle))) {
				if (!is_dir($dir.$entry) || $entry === '.' || $entry === '..') continue;
				
				// Skip directories not in our faction's list
				if ($dirsToScan !== null && !in_array($entry, $dirsToScan)) continue;
				
				$handle2 = opendir($dir.$entry);
				while ($handle2 !== false && false !== ($entry2 = readdir($handle2))){
					if ($entry2 !== '.' && $entry2 !== '..')
						$list[] = substr($entry2, 0, -4);
				}
				if ($handle2 !== false) closedir($handle2);
			}
			if ($handle !== false) closedir($handle);
			
			return $list;
		}
		
		public static function getAllShips($faction){
			// Server-side cache with 5-minute TTL
			$cacheDir = sys_get_temp_dir() . '/fv_cache';
			if (!is_dir($cacheDir)) {
				@mkdir($cacheDir, 0755, true);
			}
			
			$cacheFile = $cacheDir . '/ships_' . md5($faction ?? 'all') . '.cache';
			$cacheMaxAge = 300; // 5 minutes
			
			// Check if valid cache exists (skip if debug mode or local server)
			if (php_sapi_name() === 'cli') {
				$isLocal = true; // or false, depending on your intention
			} else {
				$serverName = $_SERVER['SERVER_NAME'] ?? '';
				$isLocal = in_array($serverName, ['localhost', '127.0.0.1']);
			}
			if (!$isLocal && file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheMaxAge) {
				$cached = @unserialize(file_get_contents($cacheFile));
				if ($cached !== false) {
					return $cached;
				}
			}
			
			// Generate fresh data - use filtered classnames for performance
			$names = self::getShipClassnames($faction);
			$ships = array();
            		$count = 0;
			foreach ($names as $name){
				if (class_exists($name)){
                    			$count++;
					$ship = new $name($count, 0, "", 0, 0, false, false, array());
					if($faction && $ship->faction != $faction){
						continue;
					}
					    foreach ($ship->systems as $system){
						$system->beforeTurn($ship, 0, 0);
					    }
        

					//enhancements (for fleet selection)
					Enhancements::setEnhancementOptions($ship);
					$ship->notesFill();
					
					if (!isset($ships[$ship->faction])){
						$ships[$ship->faction] = array();
					}
					
					$ships[$ship->faction][] = $ship;
				}
			}
			
			// Save to cache
			@file_put_contents($cacheFile, serialize($ships), LOCK_EX);
			
			return $ships;
		}
		
		/* Chameleon Sensor Suite - the vessels a disguised ship may pretend to be.
		   Per the rules: "any other kind of ship ... not a fighter ... not a base or enormous unit",
		   and same faction only (FV colours icons by TEAM, so an enemy-faction disguise renders in
		   the wrong colour and defeats itself).

		   ⚠️ This deliberately does NOT go through getAllShips(): getAllShips() calls
		   Enhancements::setEnhancementOptions() on every ship it builds, and that is where the
		   disguise option is defined - calling back into getAllShips() from there is infinite
		   recursion. Keep this self-contained.

		   Returns a list of array(phpclass, humanLabel) sorted by phpclass, with index 0 always the
		   "None" sentinel (empty phpclass) - the default, and the fallback for anything unresolvable.
		   $excludePhpclass drops the disguising ship's own class (a Dargan cannot disguise as a Dargan).*/
		public static function getDisguiseCandidates($faction, $excludePhpclass = null){
			static $memo = array(); //per-request; the underlying construction is the expensive part

			$key = (string)$faction;
			if (!isset($memo[$key])){
				$candidates = array();
				foreach (self::getShipClassnames($faction) as $name){
					if (!class_exists($name)) continue;
					$ship = new $name(0, 0, "", 0, 0, false, false, array());
					if ($ship->faction != $faction) continue;
					if (!self::isLegalDisguiseTarget($ship)) continue;
					$candidates[$ship->phpclass] = ($ship->shipClass !== '' ? $ship->shipClass : $ship->phpclass);
				}
				ksort($candidates); //stable order - the stored index means nothing without it
				$memo[$key] = $candidates;
			}

			$list = array(array('', 'None - no disguise'));
			foreach ($memo[$key] as $phpclass => $label){
				if ($excludePhpclass !== null && $phpclass === $excludePhpclass) continue;
				$list[] = array($phpclass, $label);
			}
			return $list;
		}

		/*Validate a STORED disguise choice and return its human label, or null if it is no longer
		  legal (deleted ship class, faction changed, hull retired). One ship construction, memoized,
		  so it is cheap enough to run on every in-game load of a disguised ship - which is where the
		  choice list itself is not available. Shares isLegalDisguiseTarget() with
		  getDisguiseCandidates() so the offered set and the accepted set can never drift.*/
		public static function getDisguiseLabel($faction, $phpclass, $excludePhpclass = null){
			static $memo = array();

			if (empty($phpclass)) return null;                       //"None"
			if ($phpclass === $excludePhpclass) return null;         //cannot disguise as itself

			$key = (string)$faction . '|' . $phpclass;
			if (!array_key_exists($key, $memo)){
				$label = null;
				if (class_exists($phpclass)){
					$ship = new $phpclass(0, 0, "", 0, 0, false, false, array());
					if ($ship->faction == $faction && self::isLegalDisguiseTarget($ship)){
						$label = ($ship->shipClass !== '' ? $ship->shipClass : $ship->phpclass);
					}
				}
				$memo[$key] = $label;
			}
			return $memo[$key];
		}

		public static function isLegalDisguise($faction, $phpclass, $excludePhpclass = null){
			return self::getDisguiseLabel($faction, $phpclass, $excludePhpclass) !== null;
		}

		/*The shared predicate. A simulacrum must be a real, currently-buildable ship of a size the
		  disguising hull could plausibly be mistaken for.*/
		private static function isLegalDisguiseTarget($ship){
			if ($ship instanceof FighterFlight) return false;        //"not a fighter"
			if ($ship instanceof Terrain) return false;
			if ($ship->base || $ship->smallBase) return false;       //"not a base"
			if ($ship->osat || $ship->mine) return false;
			if ($ship->Enormous) return false;                       //"not an enormous unit"
			if ($ship->shipSizeClass < 0 || $ship->shipSizeClass > 3) return false;
			//retired hulls: variantOf pointing at no real shipClass hides a ship from the lobby
			//(see the variantOf sentinel convention) - it must not be offerable as a disguise either
			if (in_array($ship->variantOf, array('NONE', 'OBSOLETE', 'OBSELETE'), true)) return false;
			return true;
		}

		public static function getAllFactions(){
			$names = self::getShipClassnames();
			$factions = array();
			$factionSet = array();
			$count = 0;
			
			foreach($names as $name){
				if (class_exists($name)){
					$count++;
					$ship = new $name($count, 0, "", 0, 0, false, false, array());
					if (!isset($factionSet[$ship->faction])){
						$factionSet[$ship->faction] = true;
						$factions[] = $ship->faction;
					}
				}
			}
			
			return $factions;
		}
	//}
	
	 //Old version before caching, keep using for static files - DK Dec 2025
	//class ShipLoader{
	
	
		public static function getShipClassnamesStatic(){
			$dir = dirname(__DIR__) ."/model/ships/";
			$handle = opendir($dir);
			$list = array();
			while ($handle !== false && false !== ($entry = readdir($handle))) {
			
				if (is_dir($dir.$entry) && $entry != "." && $entry != ".."){
					$handle2 = opendir($dir.$entry);
					
					while ($handle2 !== false && false !== ($entry2 = readdir($handle2))){
						if ($entry2 != "." && $entry2 != "..")
							$list[] = substr($entry2, 0, -4);
					}
					if ($handle2 !== false) { closedir($handle2); }
				}
					
			}
			if ($handle !== false) { closedir($handle); }
			
			return $list;
		}
		
		public static function getAllShipsStatic($faction){
			$names = self::getShipClassnamesStatic();
			$ships = array();
            		$count = 0;
			foreach ($names as $name){
				if (class_exists($name)){
                    			$count++;
					$ship = new $name($count, 0, "", 0, 0, false, false, array());
					if($faction && $ship->faction != $faction){
						continue;
					}
					    foreach ($ship->systems as $system){
						$system->beforeTurn($ship, 0, 0);
					    }
        

					//enhancements (for fleet selection)
					Enhancements::setEnhancementOptions($ship);
					$ship->notesFill();
					
					if (!isset($ships[$ship->faction])){
						$ships[$ship->faction] = array();
					}
					
					$ships[$ship->faction][] = $ship;
				}
			}
			
			return $ships;
		}
		
		public static function getAllFactionsStatic(){
			$names = self::getShipClassnamesStatic();
			$factions = array();
			$factionSet = array();
			$count = 0;
			
			foreach($names as $name){
				if (class_exists($name)){
					$count++;
					$ship = new $name($count, 0, "", 0, 0, false, false, array());
					if (!isset($factionSet[$ship->faction])){
						$factionSet[$ship->faction] = true;
						$factions[] = $ship->faction;
					}
				}
			}
			
			return $factions;
		}
		    
		//New function to suppoort new method for how staticships are loaded to help with HTTP Protocol errors - DK Dec 2025
		public static function getShipsByClass($classNames){
			$ships = array();
            $classNames = array_unique($classNames);
            
			foreach ($classNames as $name){
				if (class_exists($name)){
					$ship = new $name(0, 0, "", 0, 0, false, false, array());
                    
                    foreach ($ship->systems as $system){
						$system->beforeTurn($ship, 0, 0);
					}
        
					//enhancements (for fleet selection)
					Enhancements::setEnhancementOptions($ship);
					$ship->notesFill();
					
					if (!isset($ships[$ship->faction])){
						$ships[$ship->faction] = array();
					}
					
					$ships[$ship->faction][$ship->phpclass] = $ship;
				}
			}
			
			return $ships;
		}
	}	
	

?>
