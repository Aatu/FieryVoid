<?php

class GameRules implements JsonSerializable{

    private $rules = [];

    function __construct($rules) {
        
        $movementRules = $this->getSimultaneousMovementRules($rules);
        if ($movementRules !== null) {
            array_push($this->rules, $movementRules);
        }

    }

    private function getSimultaneousMovementRules($rules) {
        if (isset($rules['initiativeCategories'])) {
            return new SimultaneousMovementRule((int)$rules['initiativeCategories']);
        }

        return null;
    }

    public function jsonSerialize() {
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