<?php
class ZUtanor extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
	        
		$this->pointCost = 85 *6;
		$this->faction = "Yolu Confederation";
		$this->phpclass = "ZUtanor";
		$this->shipClass = "Utanor Heavy Interceptors";
		  $this->variantOf = "Utan Heavy Fighters";
		$this->imagePath = "img/ships/utan.png";
		$this->unofficial = true;     //unofficial design that actually may be useful - a common-availability heavy interceptor (rather than assault fighter like the original) - part of Yolu reevaluation effort           

        $this->isd = 2099; //let's say it's later design than original's 2050;)
		
		$this->forwardDefense = 7;
		$this->sideDefense = 9;
		$this->freethrust = 10;
		$this->offensivebonus = 6;
		$this->jinkinglimit = 6;
		$this->turncost = 0.33;
		$this->iniativebonus = 16 *5;
		
		$this->gravitic = true; 
		$this->dropOutBonus = -2; 
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(5, 4, 4, 4);
            $fighter = new Fighter("ZUtanor", $armour, 15, $this->id);
            $fighter->displayName = "Utanor";
            $fighter->imagePath = "img/ships/utan.png";
            $fighter->iconPath = "img/ships/utan_large.png";

            $fighter->addFrontSystem(new LightFusionCannon(330, 30, 4, 3));
		
			      $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack

            $this->addSystem($fighter);

        }
    }
}

?>
