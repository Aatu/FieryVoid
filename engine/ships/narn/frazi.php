<?php
class Frazi extends FighterFlight{
    
    function __construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement){
        parent::__construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement);
        
		$this->pointCost = 348;
		$this->faction = "Narn";
        $this->phpclass = "Frazi";
        $this->shipClass = "Frazi Heavy fighters";
		$this->imagePath = "ships/frazi.png";
        
        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 10;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 6;
        
		$this->iniativebonus = 80;
        
        for ($i = 0; $i<6; $i++){
			
			$armour = array(2, 2, 3, 3);
			$frazi = new Fighter($armour, 12);
			
			$frazi->imagePath = "ships/frazi.png";
			$frazi->iconPath = "ships/frazi_large.png";
			
			$this->addSystem($frazi);
			
		}
		
		
    }

}



?>
