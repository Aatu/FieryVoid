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
	}	
	

?>
