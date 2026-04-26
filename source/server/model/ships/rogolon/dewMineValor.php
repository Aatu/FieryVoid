<?php
class dewMineValor extends Mine{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 22;
        $this->faction = "Rogolon Dynasty";
        $this->phpclass = "dewMineValor";
        $this->imagePath = "img/ships/hurrMine.png";
        $this->shipClass = "Valor DEW Mine";
		$this->occurence = "common";
		//$this->variantOf = "Tonkar DEW Mine";    
        $this->isd = 2200;       
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        $this->signature = 2;
        $this->detectedSignature = 1;           
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = -200; 
        $this->notes = 'Has IFF System';
        $this->mineType = 'DEW';         
        $this->commandControl = true;   
        $this->notes = 'Has Command Controller';
       		    	    	    	    
        //Block all enhancements for Mine units when bought
		Enhancements::nonstandardEnhancementSet($this, 'Mines');	
		
		// Remove MINE_CTRL from enabled array because it's added by the 'Mines' set, but this unit already has one.
		$ccIndex = array_search('MINE_CTRL', $this->enhancementOptionsEnabled);
		if ($ccIndex !== false) {
			unset($this->enhancementOptionsEnabled[$ccIndex]);
		}   
        
	    //ammo magazine itself (AND its missile options)
	    $ammoMagazine = new AmmoMagazine(3); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 6); //add full load of basic missiles                 

	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H        

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new MagGravReactorTechnical(0, 1, 0, 1));
        $this->addPrimarySystem(new mineStealth(0, 1, 1));
        $this->addPrimarySystem(new MineControllerDEW(0, 1, 0, 20, 2, true)); //$armour, $maxhealth, $powerReq, $startArc, $endArc, $range/output, $accuracy 
        $this->addPrimarySystem(new AmmoMissileRackSO(0, 1, 0, 0, 360, $ammoMagazine, false));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(0, 6));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    20 => "Structure",
            )
        );
        
    }
}
?>
