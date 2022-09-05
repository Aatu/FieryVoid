<?php
class Tiqus extends FighterFlight{
/*	
Tiqus Attack fighter: CUSTOM Tiquincc variant for attack fighter role.
Idea description:
My proposal (instead of upgrading existing fighters) is to add a new fighter: attack variant of Tiquincc. Traits:
 - Uncommon variant
 - forward weapon switched to reasonably powerful antiship weapon
 - proposed weapon: more powerful (say, about Frazi level) (but NOT longer ranged) variant of Ionizer, dual mount but firing every other turn (and exclusive weapon) - probably with penalty vs fighters.
 - Aft weapon stays, as defensive one
Intent: this fighter will retain Tiquincc drawbacks (large profile, flimsy) and be poor as interceptor (front guns would be probably decent vs superheavy fightrs though 🙂 , otherwise single Aft gun will be the weapon of choice).
Against ships, Cascor agility plus 1/2turns weapon will mean it will make precision passes coming from weakly defended aspects, and its weapon should allow these passes to do reasonable damage - but its flimsiness would mean it cannot force its way, relying instead on harassing fighters (which Cascor should have plenty) to put cracks into targets' defensive coverage. WIll need to be escorted, but it should not be a problem for Cascor.
*/

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 45 *6;
        $this->faction = "Cascor";
        $this->phpclass = "Tiqus";
        $this->shipClass = "Tiqus Attack Fighters";
        $this->imagePath = "img/ships/CascorTiqincc.png";
        $this->isd = 2259;
		
        $this->variantOf = "Tiqincc Medium Fighters";
        $this->occurence = "uncommon";
	      $this->unofficial = true;
		  
        
        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 14;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 8;
        $this->accelcost = 2;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 18 *5;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("Tiqus", $armour, 9, $this->id);
            $fighter->displayName = "Tiqus";
            $fighter->imagePath = "img/ships/CascorTiqincc.png";
            $fighter->iconPath = "img/ships/CascorTiqincc_Large.png";

            $frontGun = new IonizerHvy(330, 30, 2);            
            $fighter->addFrontSystem($frontGun);
			
            $rearGun = new Ionizer(150, 210, 1);
            $fighter->addAftSystem($rearGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
						
            $this->addSystem($fighter);
       }
    }
}
?>
