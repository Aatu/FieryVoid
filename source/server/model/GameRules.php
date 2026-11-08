<?php

class GameRules implements JsonSerializable{

    private $rules = [];

    function __construct($rules) {
        
        $movementRules = $this->getSimultaneousMovementRules($rules);
        if ($movementRules !== null) {
            array_push($this->rules, $movementRules);
        }
        $desperateRules = $this->getDesperateRules($rules);
        if ($desperateRules !== null) {
            array_push($this->rules, $desperateRules);
        }

        $friendlyFireRules = $this->getFriendlyFireRules($rules);
        if ($friendlyFireRules !== null) {
            array_push($this->rules, $friendlyFireRules);
        }

        $allowMinesRules = $this->getAllowMinesRules($rules);
        if ($allowMinesRules !== null) {
            array_push($this->rules, $allowMinesRules);
        }

        $allowReinforcementsRules = $this->getAllowReinforcementsRules($rules);
        if ($allowReinforcementsRules !== null) {
            array_push($this->rules, $allowReinforcementsRules);
        }

        $asteroidsRules = $this->getAsteroidsRules($rules);
        if ($asteroidsRules !== null) {
            array_push($this->rules, $asteroidsRules);
        }
        $moonsRules = $this->getMoonsRules($rules);
        if ($moonsRules !== null) {
            array_push($this->rules, $moonsRules);
        }
        $dustAndMeteorsRules = $this->getDustAndMeteorsRules($rules);
        if ($dustAndMeteorsRules !== null) {
            array_push($this->rules, $dustAndMeteorsRules);
        }
        $dustZoneRules = $this->getDustZoneRules($rules);
        if ($dustZoneRules !== null) {
            array_push($this->rules, $dustZoneRules);
        }
        $nebulaRules = $this->getNebulaRules($rules);
        if ($nebulaRules !== null) {
            array_push($this->rules, $nebulaRules);
        }
        $emStormRules = $this->getEMStormRules($rules);
        if ($emStormRules !== null) {
            array_push($this->rules, $emStormRules);
        }
        $gravityWellRules = $this->getGravityWellRules($rules);
        if ($gravityWellRules !== null) {
            array_push($this->rules, $gravityWellRules);
        }
        $terrainDriftRules = $this->getTerrainDriftRules($rules);
        if ($terrainDriftRules !== null) {
            array_push($this->rules, $terrainDriftRules);
        }
        $terrainLayoutRules = $this->getTerrainLayoutRules($rules);
        if ($terrainLayoutRules !== null) {
            array_push($this->rules, $terrainLayoutRules);
        }
        $fleetTestRules = $this->getFleetTestRules($rules);
        if ($fleetTestRules !== null) {
            array_push($this->rules, $fleetTestRules);
        }

        $ladderRules = $this->getLadderRules($rules);
        if ($ladderRules !== null) {
            array_push($this->rules, $ladderRules);
        }
    }

    private function getSimultaneousMovementRules($rules) {
        if (isset($rules['initiativeCategories'])) {
            return new SimultaneousMovementRule((int)$rules['initiativeCategories']);
        }

        return null;
    }

    private function getDesperateRules($rules) {
        if (isset($rules['desperate'])) {
            return new DesperateRule((int)$rules['desperate']);
        }

        return null;
    }

    private function getFriendlyFireRules($rules) {
        if (isset($rules['friendlyFire'])) {
            return new FriendlyFireRule();
        }
        return null;
    }    

    //Never beside a Nebula: "minefields will not appear in that terrain" (KS 9.9) - see NebulaRule.
    private function getAllowMinesRules($rules) {
        if (isset($rules['allowMines']) && empty($rules['nebula'])) {
            return new AllowMinesRule();
        }
        return null;
    }

    //The Nebula (NebulaRule): the whole map, so it is only on or off.
    private function getNebulaRules($rules) {
        if (!empty($rules['nebula'])) {
            return new NebulaRule();
        }
        return null;
    }

    //The Electromagnetic Storm (EMStormRule, EM_STORM_PLAN.md): the whole map, so it is only on or off. Combines with anything.
    private function getEMStormRules($rules) {
        if (!empty($rules['emStorm'])) {
            return new EMStormRule();
        }
        return null;
    }

    //One Gravity Well (GravityWellRule, GRAVITY_WELL_PLAN.md): absent unless its strength is 1..3.
    private function getGravityWellRules($rules) {
        if (!isset($rules['gravityWell'])) return null;
        $rule = new GravityWellRule($rules['gravityWell']);
        return $rule->isEmpty() ? null : $rule;
    }

    //Drifting asteroids / meteor swarms (TerrainDriftRule): absent unless at least one of the two kinds drifts.
    private function getTerrainDriftRules($rules) {
        if (!isset($rules['terrainDrift'])) return null;
        $rule = new TerrainDriftRule($rules['terrainDrift']);
        return $rule->isEmpty() ? null : $rule;
    }

    /* REINFORCEMENTS_PLAN.md §2.1 - the Create Game rule, PLUS every Fleet Builder lobby
       (user request 2026-08-28).

       ⭐ A FLEET BUILDER ALWAYS HAS REINFORCEMENTS. A fleetTest game exists to compose and SAVE
       fleets, and a saved fleet now remembers which units were bought as reinforcements (§0) -
       so the Builder has to be able to author one, and to load one back without silently
       flattening it into the main fleet, which is what the rule gate in
       gamedata.isReinforcementRow would otherwise do.

       ⭐ DERIVED HERE, not written into games.js's rules object, and that is the whole reason it
       is in this file: a game's rules JSON is stored once at creation and never rewritten, so
       adding the key at creation would leave every Fleet Builder lobby that ALREADY EXISTS
       without it. Deriving it means one decision serves both sides too - the client reads
       gamedata.rules.allowReinforcements, which is this object's jsonSerialize.

       Same exemption the lobby already grants mines in three places (gamelobby.js:
       `!gamedata.rules.allowMines && !gamedata.rules.fleetTest`): a Fleet Builder is not a
       scenario, and nothing in it is meant to be restricted.

       ⚠️ !empty, not isset, on fleetTest alone - it mirrors the client's `fleetTest === 1` test
       rather than getFleetTestRules' looser isset, so a hypothetical `fleetTest: 0` cannot turn
       reinforcements on in an ordinary game. */
    private function getAllowReinforcementsRules($rules) {
        if (isset($rules['allowReinforcements']) || !empty($rules['fleetTest'])) {
            return new AllowReinforcementsRule();
        }
        return null;
    }

    private function getAsteroidsRules($rules) {
        if (isset($rules['asteroids'])) {
            return new AsteroidsRule((int)$rules['asteroids']);
        }

        return null;
    }

    private function getFleetTestRules($rules) {
        if (isset($rules['fleetTest'])) {
            return new FleetTestRule((int)$rules['fleetTest']);
        }
        return null;
    }

    private function getLadderRules($rules) {
        if (isset($rules['ladder'])) {
            return new LadderRule((bool)$rules['ladder']);
        }
        return null;
    }
/*
    private function getMoonsRules($rules) {
        if (isset($rules['moons'])) {
            return new MoonsRule((int)$rules['moons']);
        }

        return null;
    }  
*/

private function getMoonsRules($rules) {
    if (!isset($rules['moons'])) {
        return null;
    }

    $m = $rules['moons'];

    // Support either assoc array or stdClass
    if (is_object($m)) {
        $m = (array)$m;
    }
    if (!is_array($m)) {
        return null;
    }

    $small  = (int)($m['small']  ?? 0);
    $medium = (int)($m['medium'] ?? 0);
    $large  = (int)($m['large']  ?? 0);

    // If you only want to add the rule when at least one > 0, uncomment:
    // if ($small === 0 && $medium === 0 && $large === 0) return null;

    return new MoonsRule($small, $medium, $large);
}

    //Absent unless at least one count is above zero, so "no dust, no meteors" leaves the rules
    //blob exactly as it was before this rule existed.
    private function getDustAndMeteorsRules($rules) {
        if (!isset($rules['dustAndMeteors'])) return null;

        $d = $rules['dustAndMeteors'];
        if (is_object($d)) $d = (array)$d;
        if (!is_array($d)) return null;

        $rule = new DustAndMeteorsRule($d['dust'] ?? 0, $d['meteors'] ?? 0);
        $counts = $rule->jsonSerialize();
        if ($counts['dust'] === 0 && $counts['meteors'] === 0) return null;

        return $rule;
    }

    //The Dust Zone (DustZoneRule). Absent unless it covers something.
    private function getDustZoneRules($rules) {
        if (!isset($rules['dustZone'])) return null;

        $d = $rules['dustZone'];
        if (is_object($d)) $d = (array)$d;
        if (!is_array($d)) return null;

        $rule = new DustZoneRule($d['coverage'] ?? 0, $d['spans'] ?? null);
        return $rule->isEmpty() ? null : $rule;
    }

    /* Manager::createGame, ONCE, before the rules are first saved: settle where the dust is.
       A map template's dust hexes stop being Dust Field units and join the Dust Zone (user, 2026-11-05),
       so a game has one kind of dust and the client draws it as one mesh - and the 50% / 25% clump is
       grown on this map. Whatever hexes the client posted are replaced. */
    public function finaliseDustZone($gamespace) {
        $space = DustZoneRule::parseDustGamespace($gamespace);

        $layoutHexes = array();
        $layout = $this->getRuleByName('terrainLayout');
        if ($layout) {
            $layoutHexes = $layout->takeDustHexes($space);
            if ($layout->isEmpty()) $this->removeRuleByName('terrainLayout');
        }

        $zone = $this->getRuleByName('dustZone');
        if (!$zone && count($layoutHexes) > 0) {
            $zone = new DustZoneRule(0);
            array_push($this->rules, $zone);
        }
        if (!$zone) return;

        $zone->generateDustHexes($space, $layoutHexes);
        if ($zone->isEmpty()) $this->removeRuleByName('dustZone');
    }

    private function removeRuleByName($name) {
        $this->rules = array_values(array_filter($this->rules, function ($rule) use ($name) {
            return $rule->getRuleName() != $name;
        }));
    }

    //A map template's pre-placed terrain. Absent unless at least one unit survives the checks.
    private function getTerrainLayoutRules($rules) {
        if (!isset($rules['terrainLayout'])) return null;

        $t = $rules['terrainLayout'];
        if (is_object($t)) $t = (array)$t;
        if (!is_array($t)) return null;

        $rule = new TerrainLayoutRule($t['name'] ?? '', $t['units'] ?? array());
        return $rule->isEmpty() ? null : $rule;
    }

    public function jsonSerialize(): mixed {
        $list = [];
    
        foreach ($this->rules as $rule) {
            $list[$rule->getRuleName()] = $rule;
        }
    
        return $list;
    }
	
	/*just information whether rule exists!*/
	public function hasRuleName($name){
        foreach ($this->rules as $rule) {
            if ($rule->getRuleName() == $name) {
                return true;
            }
        }
        return false;
	}

    public function getRuleByName($name) {
        foreach ($this->rules as $rule) {
            if ($rule->getRuleName() == $name) {
                return $rule; // Return the correct rule instance
            }
        }
        return null; // Return null if no match is found
    }
    
	/*information whether _method_ exists!*/
    public function hasRule($name) {
        foreach ($this->rules as $rule) {
            if (method_exists($rule, $name)) {
                return true;
            }
        }
        return false;
    }

    public function callRule($name, $args) {
        
        if (!is_array($args)) {
            $args = [$args];
        }

        $ruleObject = $this->getRule($name);

        if ($ruleObject === null) {
            throw new Exception("Rule class was null");
        }

        return call_user_func_array(array($ruleObject, $name), $args);
    }

    private function getRule($name) {
        foreach ($this->rules as $rule) {
            if (method_exists($rule, $name)) {
                return $rule;
            }
        }

        return null;
    }
}