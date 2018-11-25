<?php

class ShipSystem
{
    public $jsClass = false;
    public $destroyed = false;
    public $startArc, $endArc;
    public $location; //0:primary, 1:front, 2:rear, 3:left, 4:right;
    public $id, $armour, $maxhealth, $powerReq, $output, $name, $displayName;
    public $outputType = null;
    public $specialAbilities = array();

    public $damage = array();
    public $boostable = false;
    public $boostEfficiency = null;
    public $maxBoostLevel = null;
    public $power = array();
    public $fireOrders = array();
    public $canOffLine = false;

    public $data = array();
    public $critData = array();
    public $destructionAnimated = false;
    public $imagePath, $iconPath;
    public $critRollMod = 0; //penalty tu critical damage roll: positive means crit is more likely, negative less likely (for this system)

    public $possibleCriticals = array();

    public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?

    public $forceCriticalRoll = false; //true forces critical roll even if no damage was done

    public $criticals = array();
    protected $advancedArmor = false; //indicates that system has advanced armor

    protected $structureSystem;

    protected $parentSystem = null;
    protected $unit = null; //unit on which system is mounted

    public function __construct($armour, $maxhealth, $powerReq, $output)
    {
        $this->armour = $armour;
        $this->maxhealth = (int) $maxhealth;
        $this->powerReq = (int) $powerReq;
        $this->output = (int) $output;
    }

    public function stripForJson()
    {
        $strippedSystem = new stdClass();
        $strippedSystem->id = $this->id;
        $strippedSystem->name = $this->name;
        $strippedSystem->damage = $this->damage;
        $strippedSystem->criticals = $this->criticals;
        $strippedSystem->critData = $this->critData;
        $strippedSystem->power = $this->power;
        $strippedSystem->specialAbilities = $this->specialAbilities;
        $strippedSystem->output = $this->output;
        $strippedSystem->outputMod = $this->getOutputMod();
        $strippedSystem->destroyed = $this->isDestroyed();

        return $strippedSystem;
    }

    public function onConstructed($ship, $turn, $phase)
    {

        if ($ship->getAdvancedArmor() == true) {
            $this->advancedArmor = true;
        }
        $this->structureSystem = $ship->getStructureSystem($this->location);
        $this->destroyed = $this->isDestroyed();
    }

    public function setUnit($unit)
    {
        $this->unit = $unit;
    }

    public function getUnit()
    {
        return $this->unit;
    }

    public function getSpecialAbilityList($list)
    {
        if ($this instanceof SpecialAbility) {
            if ($this->isDestroyed() || $this->isOfflineOnTurn()) {
                return;
            }

            foreach ($this->specialAbilities as $effect) {
                if (!isset($list[$effect])) {
                    $list[$effect] = $this->id;
                }
            }
        }

        return $list;
    }

    /*function called before firing orders are resolved; weapons with special actions (like auto-fire, combination fire, etc)
    will have their special before firing logic here (like creating additional fire orders!)
    In future, other systems may have similar needs
     */
    public function beforeFiringOrderResolution($gamedata)
    {
    }

    public function beforeTurn($ship, $turn, $phase)
    {
        $this->setSystemDataWindow($turn);
    }

    public function setDamage($damage)
    {
        $this->damage[] = $damage;
    }

    public function setDamages($damages)
    {
        $this->damage = $damages;
    }

//    public function setPowers($power){
    //        $this->power = $power;
    //    }
    //
    public function setPower($power)
    {
        $this->power[] = $power;
    }

    public function getFireOrders($turn = -1)
    {
        if ($turn < 1) {
            return $this->fireOrders;
        } else { //indicated a particular turn
            $fireOrders = array();
            foreach ($this->fireOrders as $fireOrder) {
                if ($fireOrder->turn == $turn) {
                    $fireOrders[] = $fireOrder;
                }
            }
            return $fireOrders;
        }
    }

    public function setFireOrder($fireOrder)
    {
        $this->fireOrders[] = $fireOrder;
    }

    public function setFireOrders($fireOrders)
    {
        $this->fireOrders = $fireOrders;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setCritical($critical, $turn)
    {

        if ($critical->param) {
            $currentTurn = TacGamedata::$currentTurn;
            if ($currentTurn > $critical->turn + $critical->param) {
                return;
            }
        }

        if (!$critical->oneturn || ($critical->oneturn && $critical->turn >= $turn - 1)) {
            $this->criticals[] = $critical;
        }

    }

    public function setCriticals($criticals, $turn)
    {
        foreach ($criticals as $crit) {
            $this->setCritical($crit, $turn);
        }
    }

    public function getArmour($target, $shooter, $dmgType, $pos = null)
    { //gets total armour
        $armour = $this->getArmourStandard($target, $shooter, $dmgType, $pos) + $this->getArmourInvulnerable($target, $shooter, $dmgType, $pos);
        return $armour;
    }

    public function getArmourStandard($target, $shooter, $dmgClass, $pos = null)
    { //gets standard armor - from indicated direction if necessary direction
        //$pos is to be included if launch position is different than firing unit position
        if ($this->advancedArmor != true) {
            return $this->armour;
        } else {
            return 0;
        }
    }

    public function getArmourInvulnerable($target, $shooter, $dmgClass, $pos = null)
    { //gets invulnerable part of armour (Adaptive Armor, at the moment)
        //special reductions are habndled here - technically weapons ay do that, but it began otherwise (as truly invulnerable for weapons)
        //$pos is to be included if launch position is different than firing unit position
        $armour = 0;

        if ($this->advancedArmor == true) {
            $armour += $this->armour;
            if ($dmgClass == 'Ballistic') { //extra protection against ballistics
                $armour += 2;
            }
            if ($dmgClass == 'Matter') { //slight vulnerability vs Matter
                $armour += -2;
            }
        }

        $armour = max(0, $armour); //no less than 0, BEFORE adaptive armor kicks in!

        $activeAA = 0;
        if (isset($target->adaptiveArmour)) {
            if (isset($target->armourSettings[$dmgClass][1])) {
                $activeAA = $target->armourSettings[$dmgClass][1];
                $armour += $activeAA;
            }
        }
        return $armour;
    }

    public function setSystemDataWindow($turn)
    {
        if ($this->startArc !== null) {
            $this->data["Arc"] = $this->startArc . ".." . $this->endArc;
        }

        if ($this->advancedArmor == true) {
            $this->data["Others"] = "Advanced Armor";
        }

        $counts = array();

        foreach ($this->criticals as $crit) {
            if (isset($counts[$crit->phpclass])) {
                $counts[$crit->phpclass]++;
            } else {
                $counts[$crit->phpclass] = 1;
            }

            $forturn = "";
            if ($crit->oneturn && $crit->turn == $turn) {
                $forturn = "next turn.";
            }

            if ($crit->oneturn && $crit->turn + 1 == $turn) {
                $forturn = "this turn.";
            }

            $this->critData[$crit->phpclass] = $crit->description . $forturn;
        }

    }

    public function testCritical($ship, $add = 0)
    {
        $crits = [];
        //use additional value to critical!
        $bonusCrit = $this->critRollMod + $ship->critRollMod;
        foreach ($this->criticals as $key => $value) {
            if ($value instanceof NastierCrit) {
                $bonusCrit += 1;
            }
        }

        $crits = array_values($crits); //in case some criticals were deleted!

        $damageMulti = 1;

        if ($ship instanceof OSAT) {
            if ($this->displayName == "Thruster" && sizeof($this->criticals) == 0) {
                if ($this->getTotalDamage() + $bonusCrit > ($this->maxhealth / 2)) {
                    $crit = $this->addCritical($ship, "OSatThrusterCrit");
                    $crits[] = $crit;
                }
            }
        }

        /*moved to potentially exploding systems themselves
        if ($this instanceof MissileLauncher || $this instanceof ReloadRack){
        $crit = $this->testAmmoExplosion($ship, $gamedata);
        $crits[] = $crit;
        }
        else */
        if ($this instanceof SubReactor) {
            //debug::log("subreactor, multi damage 0.5");
            $damageMulti = 0.5;
        }

        $roll = Dice::d(20) + floor(($this->getTotalDamage()) * $damageMulti) + $add + $bonusCrit;
        $criticalTypes = -1;

        foreach ($this->possibleCriticals as $i => $value) {
            if ($roll >= $i) {
                $criticalTypes = $value;
            }
        }
        if ($criticalTypes != -1) {
            if (is_array($criticalTypes)) {
                foreach ($criticalTypes as $phpclass) {
                    $crit = $this->addCritical($ship, $phpclass);
                    if ($crit) {
                        $crits[] = $crit;
                    }

                }
            } else {
                $crit = $this->addCritical($ship, $criticalTypes);
                if ($crit) {
                    $crits[] = $crit;
                }

            }
        }
        return $crits;
    }

    public function addCritical($ship, $phpclass)
    {
        $crit = new $phpclass(-1, $ship->id, $this->id, $phpclass, null);
        $crit->updated = true;
        $this->criticals[] = $crit;
        return $crit;
    }

    public function hasCritical($type, $turn = false)
    {
        $count = 0;
        foreach ($this->criticals as $critical) {
            if (strcmp($critical->phpclass, $type) == 0 && $critical->inEffect) {
                if ($turn === false) {
                    $count++;
                } else if ((($critical->oneturn && $critical->turn + 1 == $turn) || !$critical->oneturn) && $critical->turn <= $turn) {
                    $count++;
                }
            }
        }
        return $count;
    }

    public function getOutputMod()
    {
        $percentageMod = 0;
        $outputMod = 0;

        foreach ($this->criticals as $crit) {
            $outputMod += $crit->outputMod;
            $percentageMod += $crit->outputModPercentage;
        }
        //convert percentage mod to absolute value...
        if ($percentageMod != 0) {
            $outputMod += round($percentageMod * $this->output / 100);
        }

        return $outputMod;
    }

    public function getOutput()
    {

        if ($this->isOfflineOnTurn()) {
            return 0;
        }

        if ($this->isDestroyed()) {
            return 0;
        }

        $output = $this->output;
        $output -= $this->getOutputMod();
        $output = max(0, $output); //don't let output be negative!
        return $output;
    }

    public function getTotalDamage($turn = false)
    {
        $totalDamage = 0;

        foreach ($this->damage as $damage) {
            $d = ($damage->damage - $damage->armour);
            if ($d < 0 && ($damage->turn <= $turn || $turn === false)) {
                $d = 0;
            }

            $totalDamage += $d;
        }

        return $totalDamage;

    }

    public function isDestroyed($turn = false)
    {
        foreach ($this->damage as $damage) {
            if ($turn !== false && $damage->turn <= $currTurn && $damage->destroyed) {
                return true;
            } else if ($turn === false && $damage->destroyed) {
                return true;
            }
        }

        return false;
    }

    public function wasDestroyedThisTurn($turn)
    {
        foreach ($this->damage as $damage) {
            if ($damage->turn == $turn && $damage->destroyed) {
                return true;
            }
        }
        return false;
    }

    public function isDamagedOnTurn($turn)
    {
        if ($this->forceCriticalRoll) {
            return true;
        }
        //allow forced crit roll

        foreach ($this->damage as $damage) {
            if ($damage->turn == $turn || $damage->turn == -1) {
                if ($damage->damage > $damage->armour) {
                    return true;
                }

            }
        }

        return false;

    }

    public function getRemainingHealth()
    {
        $damage = $this->getTotalDamage();

        $rem = $this->maxhealth - $damage;
        if ($rem < 0) {
            $rem = 0;
        }

        return $rem;
    }

    public function isOfflineOnTurn($turn = null)
    {
        if ($turn === null) {
            $turn = TacGamedata::$currentTurn;
        }

        if ($this->parentSystem && $this->parentSystem instanceof DualWeapon) {
            return $this->parentSystem->isOfflineOnTurn($turn);
        }

        foreach ($this->power as $power) {
            if ($power->type == 1 && $power->turn == $turn) {
                return true;
            }
        }

        if ($this->hasCritical("ForcedOfflineOneTurn", $turn - 1)) {
            return true;
        }

        if ($this->hasCritical("ForcedOfflineForTurn")) {
            return true;
        }

        return false;

    }

    public function isOverloadingOnTurn($turn = null)
    {
        if ($turn === null) {
            $turn = TacGamedata::$currentTurn;
        }

//        if ($this->parentSystem && $this->parentSystem instanceof DualWeapon)
        //            return $this->parentSystem->isOverloadingOnTurn($turn);

        foreach ($this->power as $power) {
            if ($power->type == 3 && $power->turn == $turn) {
                return true;
            }
        }

        return false;

    }

    public function onAdvancingGamedata($ship, $gamedata)
    {
    }

    public function setSystemData($data, $subsystem)
    {
    }

    public function setInitialSystemData($ship)
    {
    }

}
