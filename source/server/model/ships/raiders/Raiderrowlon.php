<?php
class Raiderrowlon extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30 *6;
        $this->faction = "Raiders";
        $this->phpclass = "Raiderrowlon";
        $this->shipClass = "Rowlon Armored Fighters";
        $this->imagePath = "img/ships/UsuuthRowlon.png";
       	$this->isd = 1981;
	    
       	
        
        $this->forwardDefense = 5;
        $this->sideDefense = 7;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        $this->notes = 'Uncommon Raider variant from 2002.';
        $this->notes .= '<br>Rare Raider variant from 2004.';
        $this->notes .= '<br>Unavailable from 2007.';
        
        $this->iniativebonus = 20 *5;
        $this->populate();
    }
    
    public function populate(){
        
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        
        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 1, 2, 2);
            $rowlon = new Fighter("Rowlon", $armour, 8, $this->id);
            $rowlon->displayName = "Rowlon";
            $rowlon->imagePath = "img/ships/UsuuthRowlon.png";
            $rowlon->iconPath = "img/ships/UsuuthRowlon_Large.png";
                       
			$gun = new LightParticleBeam(330, 30, 2);					   
			$gun->displayName = "Light Particle Projector";
            $rowlon->addFrontSystem($gun); 
                    
			$rowlon->addAftSystem(new RammingAttack(0, 0, 360, $rowlon->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($rowlon);            
        }       
    }    
}



?>
