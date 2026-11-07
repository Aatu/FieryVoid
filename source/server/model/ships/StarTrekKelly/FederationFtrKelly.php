<?php
class FederationFtrKelly extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 130 *6; //for 6; a bit add-on for not requiring carrier
	    $this->faction = "Star Trek (Kelly)";
        $this->phpclass = "FederationFtrKelly";
        $this->shipClass = "Fedaration Fighter flight";
        $this->imagePath = "img/ships/StarTrek/federationFtrKelly.png";
		$this->unofficial = true;
		
        $this->isd = 2151;
		$this->notes = "Warp Engine";
		$this->notes .= "<br>No hangar required";
        //$this->limited = 10; //Restricted 10% - to go with no hangar requirements; to be decided later whether such a limitation is necessary; "ships per hull" limitation will apply (at flight level)
		$this->hangarRequired = ""; //NO hangar required

        $this->canvasSize = 64;        

        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 10;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 2;
        $this->turncost = 0.33;
		
        $this->pivotcost = 2;

    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
        
       	$this->iniativebonus = 14 *5; //default SHF ini
        $this->populate();
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(3, 2, 3, 3);
            $fighter = new Fighter("FederationFtrKelly", $armour, 25, $this->id);
            $fighter->displayName = "Fedaration Fighter";
			
            $fighter->imagePath = "img/ships/StarTrek/federationFtrKelly.png";
            $fighter->iconPath = "img/ships/StarTrek/federationFtrKelly_Large.png";
			
			
			$frontGun1 = new TrekPhaserKellyType4(330, 30, 2);
            $fighter->addFrontSystem($frontGun1);

			$fighter->addAftSystem(new TrekShieldFtr(1, 6, 3, 1) ); //armor, health, rating, recharge
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
            $this->addSystem($fighter);
        }
    }
}
?>