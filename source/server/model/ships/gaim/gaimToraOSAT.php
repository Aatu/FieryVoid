<?php
class gaimToraOSAT extends OSAT{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 250;
	$this->faction = "Gaim Intelligence";
        $this->phpclass = "gaimToraOSAT";
        $this->imagePath = "img/ships/GaimTora.png";
        $this->canvasSize = 60;
        $this->shipClass = 'Tora Defense Satellite';
        
        $this->isd = 2259;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
		
        $this->addPrimarySystem(new Reactor(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 7, 2, 6)); 
        $this->addAftSystem(new Thruster(3, 6, 0, 0, 2)); 
		
        $this->addFrontSystem(new PacketTorpedo(3, 6, 5, 270, 90)); 
        $this->addFrontSystem(new PacketTorpedo(3, 6, 5, 270, 90)); 
        $this->addFrontSystem(new ParticleConcentrator(3, 9, 7, 300, 60)); 
        $this->addFrontSystem(new ScatterGun(2, 8, 3, 180, 360)); 
        $this->addFrontSystem(new ScatterGun(2, 8, 3, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 30));
        
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "2:Thruster",
					12 => "1:Particle Concentrator",
					14 => "1:Packet Torpedo",
					16 => "1:Scattergun",
					18 => "Scanner",
					20 => "Reactor",
			)
		);
    
    
        
    }
}
?>
