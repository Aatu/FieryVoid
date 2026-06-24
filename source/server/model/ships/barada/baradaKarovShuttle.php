<?php
class baradaKarovShuttle extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30 * 6;
        $this->faction = "Barada Imperium";
        $this->phpclass = "baradaKarovShuttle";
        $this->shipClass = "Karov Assault Shuttle";
        $this->imagePath = "img/ships/baradaKarovAssaultShuttle.png";
        $this->isd = 2216;
        $this->unofficial = true;

		$this->notes = 'Non-atmospheric.';
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        $this->freethrust = 6;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'assault shuttles'; //for fleet check
    	$this->iniativebonus = 10 * 5;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("Karov", $armour, 8, $this->id);
            $fighter->displayName = "Karov";
            $fighter->imagePath = "img/ships/baradaKarovAssaultShuttle.png";
            $fighter->iconPath = "img/ships/baradaKarovAssaultShuttle_large.png";
			
			
			$gun = new LightParticleBeam(330, 30, 1);
			$gun->displayName = "Light Particle Beam";
			$fighter->addFrontSystem($gun);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            $this->addSystem($fighter);
       }
    }
}
?>
