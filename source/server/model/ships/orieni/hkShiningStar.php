<?php
class HkShiningStar extends FighterFlight{
    /*Orieni Hunter-Killer - remotely controlled suicide fighter*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 45*6;
        $this->faction = "Orieni";
        $this->phpclass = "HkShiningStar";
        $this->shipClass = "Shining Star Hunter-Killer flight";
        $this->imagePath = "img/ships/OrieniHK.png";
        
        $this->isd = 1998;
        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 6 *5;//no mistake, this is semi-autonomous unit without pilot - so its Ini is really low!
        $this->populate();     
        
        HkControlNode::addHKFlight($this);
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("HkShiningStar", $armour, 12, $this->id);
            $fighter->displayName = "Shining Star";
            $fighter->imagePath = "img/ships/OrieniHK.png";
            $fighter->iconPath = "img/ships/OrieniHK_large.png";
            
            //$armour, $startArc, $endArc, $designDamage = 0, $fcbonus = 0, $designedToRam = false, $selfDestruct = 0
            //Shining Star should by rules get a penalty of -1 per 3 speed at the moment of ram, and flat +2 bonus; I change it to a flat -1, regardless of circumstances
	    $hitPenalty = -1;
            $fighter->addFrontSystem(new RammingAttack(0, 0, 360, 80, $hitPenalty, true, 80));
            
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
