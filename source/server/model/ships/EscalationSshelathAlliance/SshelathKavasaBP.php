<?php
class SshelathKavasaBP extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 35 * 6;
        $this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathKavasaBP";
        $this->shipClass = "Kavasa Breaching Pod";
        $this->imagePath = "img/ships/EscalationWars/SshelathKavasa.png";
        $this->isd = 1910;
        
//	    $this->notes = 'Non-atmospheric.';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
        $this->turncost = 0.33;

        $this->maxFlightSize = 2;//this is an unusual type of 'fighter', limit flight size.      
        $this->hangarRequired = 'Breaching Pods'; //for fleet check   
		$this->unitSize = 1; 		

		$this->notes = "Bonus to delivery roll for marines.";
		
    	$this->iniativebonus = 9 * 5;
        $this->populate();
    
    	$this->enhancementOptionsEnabled[] = 'ELT_MAR'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MAR'; //To enable extra Marines enhancement
		
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("SshelathKavasaBP", $armour, 16, $this->id);
            $fighter->displayName = "Kavasa";
            $fighter->imagePath = "img/ships/EscalationWars/SshelathKavasa.png";
            $fighter->iconPath = "img/ships/EscalationWars/SshelathKavasa_large.png";

			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.

			$gun = new LightParticleBeam(330, 30, 1, 1);
			$gun->displayName = "Ultralight Particle Beam";
			$fighter->addFrontSystem($gun);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            $this->addSystem($fighter);
       }
    }
}
?>
