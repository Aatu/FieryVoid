<?php
class QomYominTalRenHK extends FighterFlight{
    /*Orieni Hunter-Killer - remotely controlled suicide fighter*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 35*6;
        $this->faction = "ZNexus Makar Federation";
        $this->phpclass = "QomYominTalRenHK";
        $this->shipClass = "Tal Ren Hunter-Killer flight";
			$this->variantOf = "Tol Mor Armed Drone";
			$this->occurence = "rare";
        $this->imagePath = "img/ships/Nexus/makar_talren.png";
        
        $this->isd = 1930;
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 50;//no mistake, this is semi-autonomous unit without pilot - so its Ini is really low!
        $this->populate();     
        
        HkControlNode::addHKFlight($this);
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(1, 1, 0, 0);
            $fighter = new Fighter("QomYominTalRenHK", $armour, 6, $this->id);
            $fighter->displayName = "Tal Ren";
            $fighter->imagePath = "img/ships/Nexus/makar_talren.png";
            $fighter->iconPath = "img/ships/Nexus/makar_talren_large.png";
            
            //$armour, $startArc, $endArc, $designDamage = 0, $fcbonus = 0, $designedToRam = false, $selfDestruct = 0
            //Shining Light should by rules get a penalty of -1 per 2 speed at the moment of ram, and flat +1 bonus
	    //...and I do it so
	    $hitPenalty = 2; //a bonus, actually!
	    $ram = new RammingAttack(0, 0, 360, 30, $hitPenalty, true, 0);
	    $ram->rangePenalty = 0.5; //-1/2 hexes
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