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

        $asteroidsRules = $this->getAsteroidsRules($rules);
        if ($asteroidsRules !== null) {
            array_push($this->rules, $asteroidsRules);
        }
        $moonsRules = $this->getMoonsRules($rules);
        if ($moonsRules !== null) {
            array_push($this->rules, $moonsRules);
        }
        $fleetTestRules = $this->getFleetTestRules($rules);
        if ($fleetTestRules !== null) {
            array_push($this->rules, $fleetTestRules);
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