<?php
class DrakhRaiderFlt extends FighterFlight{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 150*6;
        $this->faction = "Drakh";
        $this->phpclass = "DrakhRaiderFlt";
        $this->shipClass = "Raider Assault Fighters";
        $this->imagePath = "img/ships/DrakhRaider.png";
        
        $this->isd = 2210;
        $this->unofficial = true;
        $this->gravitic = true;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 10;
        $this->offensivebonus = 7;
        $this->jinkinglimit = 4; //deliberately low jinking limit and Init
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 14 *5; 
	    $this->advancedArmor = true; 
        
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(4, 3, 4, 4);
            $fighter = new Fighter("DrakhRaiders", $armour, 12, $this->id);
            $fighter->displayName = "Raider Assault Fighters";
            $fighter->imagePath = "img/starwars/DrakhRaider.png";
            $fighter->iconPath = "img/starwars/DrakhRaider_large.png"; 

            
        	$fighter->addFrontSystem(new customLtPhaseDisruptor(330, 30));

		$CombPhaseDisruptor = new LightGravitonBeam(330, 30, 0); //intended as Combined mode for LtPhaseDisruptor main weapon
		$CombPhaseDisruptor->displayName = "Combined Phase Disruptor";
 		$CombPhaseDisruptor->exclusive = true;
        	$fighter->addFrontSystem($CombPhaseDisruptor);
            
       		//Absorbtion Shield, 1 points
        	$fighter->addAftSystem(new AbsorbtionShield(0, 1, 0, 1, 0, 360));
            
        	$this->addSystem($fighter);
       }
    }
    
    
}
?>
