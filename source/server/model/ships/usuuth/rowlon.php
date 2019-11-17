<?php
class Rowlon extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30 *6;
        $this->faction = "Usuuth";
        $this->phpclass = "Rowlon";
        $this->shipClass = "Rowlon Armored Fighter";
        $this->imagePath = "img/ships/UsuuthRowlon.png";
        
        $this->forwardDefense = 5;
        $this->sideDefense = 7;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
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
                    
            $this->addSystem($rowlon);            
        }       
    }    
}



?>
