<?php
class hkBlazingStarGC extends FighterFlight{
    /*Orieni Hunter-Killer - remotely controlled suicide fighter*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 50*6;
        $this->faction = "Great Crusade Orieni Imperium";
        $this->phpclass = "hkBlazingStarGC";
        $this->shipClass = "Blazing Star Hunter-Killer flight";
        $this->imagePath = "img/ships/GChk.png";

		$this->unofficial = true;
        
        $this->isd = 2260;
        $this->forwardDefense = 8;
        $this->sideDefense = 6;
        $this->freethrust = 11;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'medium'; //for fleet check; HKs require medium fighter hangar space
        $this->deploysInHangar = true;        	
        $this->iniativebonus = 6 *5;//no mistake, this is semi-autonomous unit without pilot - so its Ini is really low!
        
        $this->populate();     
        
        HkControlNode::addHKFlight($this);
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(3, 3, 2, 2);
            $fighter = new Fighter("hkBlazingStarGC", $armour, 12, $this->id);
            $fighter->displayName = "Blazing Star";
            $fighter->imagePath = "img/ships/GChk.png";
            $fighter->iconPath = "img/ships/GChk_large.png";
            
            //$armour, $startArc, $endArc, $designDamage = 0, $fcbonus = 0, $designedToRam = false, $selfDestruct = 0
            //Shining Star should by rules get a penalty of -1 per 3 speed at the moment of ram, and flat +2 bonus;
	    $hitPenalty = 3; //a bonus, actually!
	    $ram = new RammingAttack(0, 0, 360, 80, $hitPenalty, true, 80);
	    $ram->rangePenalty = 0.25; //-1/4 hexes
            $fighter->addFrontSystem($ram);
            
            $this->addSystem($fighter);
        }
    }//endof function populate
    
    
    public function getInitiativebonus($gamedata){
        $iniBonus = parent::getInitiativebonus($gamedata);
	$iniBonus += HkControlNode::getIniMod($this->userid,$gamedata);
        return $iniBonus;
    }	
    
}
?>
