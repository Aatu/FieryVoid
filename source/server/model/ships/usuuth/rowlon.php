<?php
class Rowlon extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 180;
        $this->faction = "usuuth";
        $this->phpclass = "Rowlon";
        $this->shipClass = "Rowlon Armoured Fighter";
        $this->imagePath = "img/ships/frazi.png";
        
        $this->forwardDefense = 5;
        $this->sideDefense = 7;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
        $this->iniativebonus = 20*4;
        $this->populate();
    }
    
    public function populate(){
        
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        
        for ($i = 0; $i < $toAdd; $i++){
            
            $armour = array(2, 1, 2, 2);
            $rowlon = new Fighter("rowlon", $armour, 8, $this->id);
            $rowlon->displayName = "Rowlong Armoured Fighter";
            $rowlon->imagePath = "img/ships/frazi.png";
            $rowlon->iconPath = "img/ships/frazi_large.png";
                       
            $rowlon->addFrontSystem(new LightParticleBeam(330, 30, 2)); 
            //technically called a light particle projector, but same states as the light particle beam
                    
            $this->addSystem($rowlon);            
        }       
    }    
}



?>
