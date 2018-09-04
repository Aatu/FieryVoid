<?php
class HkShiningLight extends FighterFlight{
    /*Orieni Hunter-Killer - remotely controlled suicide fighter*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 32*6;
        $this->faction = "Orieni";
        $this->phpclass = "HkShiningLight";
        $this->shipClass = "Shining Light Hunter-Killer flight";
        $this->imagePath = "img/ships/OrieniHK.png";
        
        $this->isd = 1778;
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'medium'; //for fleet check; HKs require medium fighter hangar space
    	$this->iniativebonus = 6 *5;//no mistake, this is semi-autonomous unit without pilot - so its Ini is really low!
        $this->populate();     
        
        HkControlNode::addHKFlight($this);
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter("HkShiningLight", $armour, 9, $this->id);
            $fighter->displayName = "Shining Light";
            $fighter->imagePath = "img/ships/OrieniHK.png";
            $fighter->iconPath = "img/ships/OrieniHK_large.png";
            
            //$armour, $startArc, $endArc, $designDamage = 0, $fcbonus = 0, $designedToRam = false, $selfDestruct = 0
            //Shining Light should by rules get a penalty of -1 per 2 speed at the moment of ram, and flat +1 bonus; I change it to a flat -3, regardless of circumstances
	    $hitPenalty = -3;
            $fighter->addFrontSystem(new RammingAttack(0, 0, 360, 60, $hitPenalty, true, 60));
            
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
