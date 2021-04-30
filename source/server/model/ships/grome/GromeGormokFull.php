<?php
class GromeGormokFull extends OSAT{
/*Gormak with fully decked out railguns with special shells!*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
/*To account for the increased flexibility a points increase is needed as we cannont, yet,
purchase individual shells individually. To do this, I am assuming that each railgun is
equipped with 2 of the most expensive shells (heavies). Most shells are cheaper, but using
heavies ensures a realatively fair cost as a player is unlikely to use a standard shell 
on one of these "full" units. The final point total will be adjusted to be divisible by 5.
The costs before rounding the final value are: 
			Heavy railgun ~36 points
			Railgun ~24 points
			Light railgun ~12 points
		Estimated cost is 150 + (1x36) + (2x12) = 210*/
	$this->pointCost = 210;
	$this->faction = "Grome";
        $this->phpclass = "GromeGormokFull";
		$this->variantOf = "Gormok Orbital Satellite";
        $this->imagePath = "img/ships/GromeGormok.png";
        $this->canvasSize = 60;
        $this->shipClass = 'Gormok Orbital Satellite (full)';
	    $this->unofficial = true;
		
	    $this->notes = 'Antiquated Sensors (cannot be boosted).';
	    $this->notes .= '<br>Targeting Array treated as a 1 point sensor.';
        
        $this->isd = 2240;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
		
        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 4, 2, 3)); 
		$targetingArray = new AntiquatedScanner(3, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
        $this->addPrimarySystem(new Thruster(4, 6, 0, 0, 2)); 
        $this->addPrimarySystem(new FlakCannon(2, 4, 2, 0, 360)); 
        $this->addPrimarySystem(new GromeHvyRailGun(3, 12, 9, 300, 60)); 
        $this->addPrimarySystem(new GromeLgtRailgun(3, 6, 3, 180, 360));
        $this->addPrimarySystem(new GromeLgtRailgun(3, 6, 3, 0, 180));
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(3, 35));
        
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Thruster",
					11 => "Targeting Array",
					13 => "Heavy Railgun",
					15 => "Light Railgun",
					17 => "Antiquated Scanner",
					19 => "Reactor",
					20 => "Flak Cannon",
			)
		);
    
    
        
    }
}
?>
