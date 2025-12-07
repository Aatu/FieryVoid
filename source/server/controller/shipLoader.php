<?php

	class ShipLoader{
	
	
    private static $factionMapCacheFile = __DIR__ . '/../cache/faction_ship_map.json';

    /**
     * Returns a map of Faction => [List of Directory Names]
     */
    private static function getFactionMap() {
        if (file_exists(self::$factionMapCacheFile)) {
            $map = json_decode(file_get_contents(self::$factionMapCacheFile), true);
            if ($map && is_array($map)) {
                return $map;
            }
        }
        return self::rebuildFactionMap();
    }

    /**
     * Scans all subdirectories in model/ships/ to build the faction map.
     * Caches the result to a JSON file.
     * Uses Regex to avoid instantiating thousands of objects.
     */
    private static function rebuildFactionMap() {
        $baseDir = dirname(__DIR__) . "/model/ships/";
        $handle = opendir($baseDir);
        $map = array();

        if ($handle) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry === "." || $entry === "..") continue;
                
                $fullPath = $baseDir . $entry;
                if (is_dir($fullPath)) {
                    // Scan ALL files in the directory to find ALL factions present
                    $subHandle = opendir($fullPath);
                    $factionsInDir = array();
                    
                    if ($subHandle) {
                        while (false !== ($file = readdir($subHandle))) {
                            if (substr($file, -4) === ".php") {
                                // Use Regex to read faction name without loading class
                                $content = file_get_contents($fullPath . '/' . $file);
                                if (preg_match('/this->faction\s*=\s*["\'](.*?)["\']\s*;/', $content, $matches)) {
                                    $factionsInDir[$matches[1]] = true;
                                }
                            }
                        }
                        closedir($subHandle);
                    }

                    // Register this directory for every faction found in it
                    foreach (array_keys($factionsInDir) as $foundFaction) {
                        if (!isset($map[$foundFaction])) {
                            $map[$foundFaction] = array();
                        }
                        if (!in_array($entry, $map[$foundFaction])) {
                            $map[$foundFaction][] = $entry;
                        }
                    }
                }
            }
            closedir($handle);
        }

        // Cache the result
        if (!is_dir(dirname(self::$factionMapCacheFile))) {
             mkdir(dirname(self::$factionMapCacheFile), 0777, true);
        }
        
        $json = json_encode($map);
        if ($json !== false) {
            file_put_contents(self::$factionMapCacheFile, $json);
        }
        
        return $map;
    }


    public static function getShipClassnames($specificDirs = null){
        $baseDir = dirname(__DIR__) ."/model/ships/";
        $list = array();

        // If specific dirs provided, only scan those. Otherwise scan all.
        $dirsToScan = $specificDirs;

        if ($dirsToScan === null) {
            // Scan everything (legacy behavior or fallback)
            $handle = opendir($baseDir);
            $dirsToScan = array();
            while ($handle !== false && false !== ($entry = readdir($handle))) {
                 if (is_dir($baseDir.$entry) && $entry != "." && $entry != ".."){
                     $dirsToScan[] = $entry;
                 }
            }
            if ($handle !== false) { closedir($handle); }
        }

        foreach ($dirsToScan as $dirName) {
            $fullPath = $baseDir . $dirName;
            if (is_dir($fullPath)) {
                $handle2 = opendir($fullPath);
                while ($handle2 !== false && false !== ($entry2 = readdir($handle2))){
                    if ($entry2 != "." && $entry2 != ".." && substr($entry2, -4) === ".php")
                        $list[] = substr($entry2, 0, -4);
                }
                if ($handle2 !== false) { closedir($handle2); }
            }
        }
        
        return $list;
    }
    
    public static function getAllShips($faction){
        // Step 1: Get the map to find which directories contain this faction's ships
        $map = self::getFactionMap();
        
        // Step 2: Determine directories to scan
        $dirsToScan = null;
        if ($faction && isset($map[$faction])) {
            $dirsToScan = $map[$faction];
        } 
        
        // If faction requested but not found in map, return empty (or fallback to full scan if you prefer safety)
        if ($faction && $dirsToScan === null) {
            // Optimistic check: maybe cache is stale? Retry logic could go here, 
            // but for now let's assume if it's not in map, it's not a valid faction or new custom one.
            // Let's force a rebuild once just in case.
            $map = self::rebuildFactionMap();
             if ($faction && isset($map[$faction])) {
                $dirsToScan = $map[$faction];
            } else {
                return array(); // Faction not found even after rebuild
            }
        }

        // Step 3: Get only relevant classnames
        $names = self::getShipClassnames($dirsToScan);
        
        $ships = array();
        $count = 0;
        foreach ($names as $name){
            // Use autoloading/class_exists
            if (class_exists($name)){
                $count++;
                $ship = new $name($count, 0, "", 0, 0, false, false, array());
                
                // Double check faction match (in case multiple factions share a folder, though unlikely with this logic)
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
    
    public static function getAllFactions(){
        // Much faster: just return keys from the map!
        $map = self::getFactionMap();
        return array_keys($map);
    }
}
	
	

?>
