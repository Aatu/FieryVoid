<?php
class gaimIttakaShuttle extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30*6;
        $this->faction = "Gaim";
        $this->phpclass = "gaimIttakaShuttle";
        $this->shipClass = "It'Taka Assault Shuttles";
        $this->imagePath = "img/ships/gaimIttaka.png";
		
        $this->isd = 2251;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 9;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->pivotcost = 2; //shuttles have pivot cost higher
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		
        $this->iniativebonus = 45;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("giamIttakaShuttle", $armour, 12, $this->id);
            $fighter->displayName = "It'Taka";
            $fighter->imagePath = "img/ships/gaimIttaka.png";
            $fighter->iconPath = "img/ships/gaimIttaka_large.png";

			$gun = new LightParticleBeam(330, 30, 3, 1);
			$gun->displayName = "Light Particle Beam";
			$fighter->addFrontSystem($gun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
