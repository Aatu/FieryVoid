<?php
class HyachLaricha extends FighterFlight
{
	public $HyachSpecialists;
	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 40 *6;
		$this->faction = "Hyach Gerontocracy";
		$this->phpclass = "HyachLaricha";
		$this->shipClass = "Laricha Assault Shuttles";
		$this->imagePath = "img/ships/HyachLaricha.png";
				
		$this->isd = 2244;

		$this->forwardDefense = 9;
		$this->sideDefense = 7;
		$this->freethrust = 8;
		$this->offensivebonus = 4;
		$this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
		$this->turncost = 0.33;
        $this->gravitic = true;
		$this->hangarRequired = 'assault shuttles'; //for fleet check        

		$this->iniativebonus = 9*5;
		$this->populate();
	}

	public function populate()
	{
		$current = count($this->systems);
		$new = $this->flightSize;
		$toAdd = $new - $current;

		for ($i = 0; $i < $toAdd; $i++) {

			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("HyachLaricha", $armour, 10, $this->id);
			$fighter->displayName = "Laricha";
			$fighter->imagePath = "img/ships/HyachLaricha.png";
			$fighter->iconPath = "img/ships/HyachLaricha_Large.png";

            $frontGun = new LtBlastLaser(330, 30); //1 gun
            $fighter->addFrontSystem($frontGun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);

		}
	}
}


?>