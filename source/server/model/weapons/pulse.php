<?php

class Pulse extends Weapon{
        
        public $trailColor = array(190, 75, 20);
        public $animationColor = array(190, 75, 20);
        public $grouping = 20;
        public $rof = 1;
        public $maxpulses = 6;
        public $priority = 5;
	public $damageType = 'Pulse'; //indicates that this weapon does damage in Pulse mode
    	public $weaponClass = "Particle"; //(first letter upcase) weapon class - overrides $this->data["Weapon type"] if set!	
	private $useDie = 5; //die used for base number of hits

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
		/* 
            $this->data["Weapon type"] = "Particle";
            $this->data["Damage type"] = "Standard";
            $this->data["Firing Mode"] = "Pulse";
            $this->data["Grouping range"] = $this->grouping + "%";
            $this->data["Max pulses"] = $this->maxpulses;
            $this->data["Pulses"] = 'D 5';
	    */
		 $this->data["Special"] = 'Pulse mode: D'.$this->useDie.' +1/'. $this->grouping."%, max. ".$this->maxpulses." pulses";

            $this->defaultShots = $this->maxpulses;
            
            parent::setSystemDataWindow($turn);
        }
        
        protected function getPulses($turn)
        {
            return Dice::d($this->useDie);
        }
	
        protected function getExtraPulses($needed, $rolled)
        {
            return floor(($needed - $rolled) / ($this->grouping));
        }
	
	public function rollPulses($turn, $needed, $rolled){
		$pulses = $this->getPulses($turn);
		$pulses+= $this->getExtraPulses($needed, $rolled);
		$pulses=min($pulses,$this->maxpulses);
		return $pulses;
	}
	
	//Pulse weapon usually have fixed damage, so... non-fixed damage weapons would have to override as usual
        public function setMinDamage(){     $this->getDamage();      }
        public function setMaxDamage(){     $this->getDamage();      }

	
/*no longer needed, code left just in case	
        public function fire($gamedata, $fireOrder){

            $shooter = $gamedata->getShipById($fireOrder->shooterid);
            $target = $gamedata->getShipById($fireOrder->targetid);
            $this->firingMode = $fireOrder->firingMode;

            $pos = $shooter->getCoPos();

            $this->calculateHit($gamedata, $fireOrder);
            $intercept = $this->getIntercept($gamedata, $fireOrder);
            $pulses = $this->getPulses($gamedata->turn); 

            $fireOrder->notes .= " pulses: $pulses";
            $fireOrder->shots = $this->maxpulses;
            $needed = $fireOrder->needed;
            $rolled = Dice::d(100);
            if ($rolled > $needed && $rolled <= $needed+($intercept*5)){
                //$fireOrder->pubnotes .= "Shot intercepted. ";
                $fireOrder->intercepted += $pulses;
            }

            if ($rolled <= $needed){
                $extra = $this->getExtraPulses($needed, $rolled);
                $fireOrder->notes .= " extra pulses: $extra";
                $pulses += $extra;
                if ($pulses > $this->maxpulses)
                    $pulses = $this->maxpulses;

                $fireOrder->shotshit = $pulses;
                for ($i=0;$i<$pulses;$i++){
                    $this->beforeDamage($target, $shooter, $fireOrder, $pos, $gamedata);
                }
            }

            $fireOrder->rolled = 1;//Marks that fire order has been handled
        }
    */

        
        /*
        public function damage($target, $shooter, $fireOrder){

            $extra = ($fireOrder->needed - $fireOrder->rolled) % ($this->grouping*5);
            $pulses = $this->getPulses() + $extra;
            if ($pulses > $this->maxpulses)
                $pulses = $this->maxpulses;

            for ($i=0;$i<$pulses;$i++){

                if ($target->isDestroyed())
                    return;

                $system = $target->getHitSystem($shooter, $fireOrder->turn);

                if ($system == null)
                    return;

                $this->doDamage($target, $shooter, $system, $this->getFinalDamage($shooter, $target), $fireOrder);

            }


        }
        */
} //endof class Pulse



class EnergyPulsar extends Pulse{
        public $name = "energyPulsar";
        public $displayName = "Energy Pulsar";
        public $animation = "trail";
        public $animationWidth = 5;
        public $projectilespeed = 13;
        public $animationExplosionScale = 0.30;
        public $rof = 2;
        public $trailLength = 12;
        public $grouping = 25;
        public $maxpulses = 3;
        public $priority = 4;
	private $useDie = 2; //die used for base number of hits	

        public $loadingtime = 2;
        
        public $rangePenalty = 1;
        public $fireControl = array(1, 3, 3); // fighters, <mediums, <capitals 
        
        public $intercept = 1;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }


        public function setSystemDataWindow($turn){
            
            parent::setSystemDataWindow($turn);
            //$this->data["Pulses"] = 'D 2';
        }

        /*
        protected function getPulses($turn)
        {
            return Dice::d(2);
        }*/
        
        public function getDamage($fireOrder){        return 10;   }
        //public function setMinDamage(){     $this->minDamage = 10 ;      }
        //public function setMaxDamage(){     $this->maxDamage = 10 ;      }

}
    


class ScatterPulsar extends Pulse{

        public $name = "scatterPulsar";
        public $displayName = "Scatter Pulsar";
        public $animation = "trail";
        public $trailLength = 12;
        public $animationWidth = 4;
        public $projectilespeed = 13;
        public $animationExplosionScale = 0.10;
        public $rof = 3;
        public $grouping = 25;
        public $maxpulses = 6;
        
        public $loadingtime = 1;
        public $intercept = 2;
        public $priority = 3;
        
        public $rangePenalty = 2;
        public $fireControl = array(3, 2, 1); // fighters, <mediums, <capitals
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 6;   }
    }
    



class QuadPulsar extends Pulse{

        public $name = "quadPulsar";
        public $displayName = "Quad Pulsar";
        public $animation = "trail";
        public $trailLength = 20;
        public $animationWidth = 6;
        public $projectilespeed = 20;
        public $animationExplosionScale = 0.25;
        public $rof = 3;
        public $grouping = 25;
        public $maxpulses = 4;
        public $priority = 6;
	private $useDie = 3; //die used for base number of hits	
        
        public $loadingtime = 3;
        
        public $rangePenalty = 0.33;
        public $fireControl = array(-1, 3, 3); // fighters, <mediums, <capitals 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){            
            parent::setSystemDataWindow($turn);
            //$this->data["Pulses"] = 'D 3';            
        }
/*
        protected function getPulses($turn)
        {
            return Dice::d(3);
        }
*/
        public function getDamage($fireOrder){        return 14;   }
    }
    
    
    class LightPulse extends Pulse{

        public $name = "lightPulse";
        public $displayName = "Light Pulse Cannon";
        public $animation = "trail";
        public $animationWidth = 3;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.15;
        public $rof = 2;
        public $trailLength = 10;
        
        public $loadingtime = 1;
        public $priority = 3;
        
        public $rangePenalty = 2;
        public $fireControl = array(4, 3, 3); // fighters, <mediums, <capitals 
        
        public $intercept = 2;

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 8;   }
/*	    
        public function setMinDamage(){     $this->minDamage = 8 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 8 - $this->dp;      }
*/
    }
    
    class MediumPulse extends Pulse{

        public $name = "mediumPulse";
        public $displayName = "Medium Pulse Cannon";
        public $animation = "trail";
        public $trailLength = 15;
        public $animationWidth = 4;
        public $projectilespeed = 15;
        public $animationExplosionScale = 0.17;
        public $rof = 2;
        
        public $loadingtime = 2;
        public $priority = 4;
        
        public $rangePenalty = 1;
        public $fireControl = array(1, 3, 4); // fighters, <mediums, <capitals 
        
        public $intercept = 2;
        
        

        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 10;   }
/*	    
        public function setMinDamage(){     $this->minDamage = 10 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 10 - $this->dp;      }
*/
    }
    
    class HeavyPulse extends Pulse{

        public $name = "heavyPulse";
        public $displayName = "Heavy Pulse Cannon";
        public $animation = "trail";
        public $trailLength = 20;
        public $animationWidth = 5;
        public $projectilespeed = 20;
        public $animationExplosionScale = 0.20;
        public $rof = 2;
        public $priority = 6;
        
        public $loadingtime = 3;
        
        public $rangePenalty = 0.5;
        public $fireControl = array(-1, 3, 4); // fighters, <mediums, <capitals 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return 15;   }
/*	    
        public function setMinDamage(){     $this->minDamage = 15 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 15 - $this->dp;      }
*/
    }
    
    
    class GatlingPulseCannon extends Weapon{  //this is NOT a Pulse weapon at all...

        public $name = "gatlingPulseCannon";
        public $displayName = "Gatling Pulse Cannon";
        public $animation = "beam";
        public $animationWidth = 4;
        public $projectilespeed = 10;
        public $animationExplosionScale = 0.15;
        public $trailLength = 10;
        public $trailColor = array(190, 75, 20);
        public $animationColor = array(190, 75, 20);
        
        public $intercept = 2;
        
        public $loadingtime = 1;
        
        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals 
    public $damageType = "Standard";
    public $weaponClass = "Particle"; 

        function __construct($startArc, $endArc){
            parent::__construct(0, 1, 0, $startArc, $endArc);
           
        }
        
        public function setSystemDataWindow($turn){
/*
            $this->data["Weapon type"] = "Pulse";
            $this->data["Damage type"] = "Standard";
  */          
            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6,2)+6;   }
        public function setMinDamage(){     $this->minDamage = 8 /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 18 /*- $this->dp*/;      }

    }


    class MolecularPulsar extends Pulse
    {
        public $name = "molecularPulsar";
        public $displayName = "Molecular Pulsar";
        public $iconPath = "mediumPulse.png";
        public $animation = "trail";
        public $trailLength = 15;
        public $animationWidth = 4;
        public $projectilespeed = 25;
        public $animationExplosionScale = 0.17;
        public $animationColor =  array(175, 225, 175);
        public $trailColor = array(110, 225, 110);
        public $rof = 2;
        public $maxpulses = 7;
        public $grouping = 15;
        public $priority = 4;

        public $loadingtime = 1;
    	public $normalload = 2;

        public $rangePenalty = 1;
        public $fireControl = array(2, 3, 4); // fighters, <mediums, <capitals

	    public $damageType = "Pulse"; 
	    public $weaponClass = "Molecular"; 
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        
        public function setSystemData($data, $subsystem){
	    parent::setSystemData($data, $subsystem);
            if ($this->turnsloaded == 1)
            {
                $this->maxpulses = 3;
            }
            else
            {
                $this->maxpulses = 7;
            }
		
	}
        
        public function setSystemDataWindow($turn){
            parent::setSystemDataWindow($turn);
		/*
            if ($this->turnsloaded == 1){
                $this->data["Pulses"] = 'D 3';
                $this->data["Grouping range"] = 'NONE';
            }
            else {
                $this->data["Pulses"] = 'D 5';
            }
            $this->data["Weapon type"] = "Molecular";
            */
		$this->data["Special"] = 'Pulse mode fully armed: D 5 +1/15 %, max. 7 pulses';
		$this->data["Special"] .= 'Pulse mode 1 turn: D 3 pulses';
        }
        

        protected function getPulses($turn)
        {
            if ($this->turnsloaded == 1)
            {
                return Dice::d(3);
            }
            else
            {
                return Dice::d(5);
            }
            
        } 
	    
        protected function getExtraPulses($needed, $rolled)
        {
            if ($this->turnsloaded == 1)
            {
                return 0;
            }
            else
            {
                return parent::getExtraPulses($needed, $rolled);
            }
        }

        public function getDamage($fireOrder){ return 10; }
 
/*
        public function setMinDamage()
        {
            $this->minDamage = 10 - $this->dp;
        }

        public function setMaxDamage()
        {
            $this->maxDamage = 10 - $this->dp;
        }
	*/
        
    }



    class PairedLightBoltCannon extends LinkedWeapon{

        public $name = "pairedLightBoltCannon";
        public $displayName = "Light Bolt Cannon";
        public $animation = "trail";
        public $animationColor = array(30, 170, 255);
        public $animationExplosionScale = 0.10;
        public $projectilespeed = 12;
        public $animationWidth = 2;
        public $trailLength = 10;

        public $intercept = 2;

        public $loadingtime = 1;
        public $shots = 2;
        public $defaultShots = 2;

        public $rangePenalty = 2;
        public $fireControl = array(0, 0, 0); // fighters, <mediums, <capitals
        private $damagebonus = 0;
	    
	    public $damageType = "Standard"; 
	    public $weaponClass = "Particle"; 

        function __construct($startArc, $endArc, $damagebonus, $nrOfShots = 2){
            $this->damagebonus = $damagebonus;
            $this->defaultShots = $nrOfShots;
            $this->shots = $nrOfShots;

            parent::__construct(0, 1, 0, $startArc, $endArc);

        }

        public function setSystemDataWindow($turn){

            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return Dice::d(6)+$this->damagebonus;   }
        public function setMinDamage(){     $this->minDamage = 1+$this->damagebonus /*- $this->dp*/;      }
        public function setMaxDamage(){     $this->maxDamage = 6+$this->damagebonus /*- $this->dp*/;      }
    }



    class PointPulsar extends Weapon //this is NOT a Pulse weapon, disregard Pulse-specific settings...
    {
        public $name = "pointPulsar";
        public $displayName = "Point Pulsar";
        public $iconPath = "pointPulsar.png";
        public $animation = "trail";
        public $trailLength = 13;
        public $animationWidth = 4;
        public $projectilespeed = 25;
        public $animationExplosionScale = 0.17;
        public $animationColor =  array(175, 225, 175);
        public $trailColor = array(110, 225, 110);
        //public $rof = 2;
	    public $guns = 3; //always 3, completely separate (not Pulse!) shots
        public $maxpulses = 3;
        public $grouping = 0;
        public $loadingtime = 2;
        public $normalload = 2;
	    
        public $priority = 2; //due to sniping bonus
        
        public $calledShotMod = -4; //instead of usual -8
	    
        public $intercept = 2; //should be 3, but then intercept should be like a Pulse weapon - just once... call this a compromise!
	    
        public $rangePenalty = 0.5;
        public $fireControl = array(-4, 3, 5); // fighters, <mediums, <capitals
	    
	    public $damageType = "Standard"; 
	    public $weaponClass = "Particle"; 
	    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
	    
        public function setSystemDataWindow($turn){
            //$this->data["Weapon type"] = "Particle";
            //$this->data["Damage type"] = "Standard";
            
            parent::setSystemDataWindow($turn);
            //$this->data["Pulses"] = '3';
            //unset($this->data["Grouping range"]);
            //unset($this->data["Max pulses"]);
		
		$this->data["Special"] = "Called shot penalty halved";
        }
        
        public function getDamage($fireOrder){
            return 10 /*- $this->dp*/;
        }
 
        public function setMinDamage()
        {
            $this->minDamage = 10 /*- $this->dp*/;
        }
        public function setMaxDamage()
        {
            $this->maxDamage = 10 /*- $this->dp*/;
        }
        
/* no longer needed
        public function damage($target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location = null){
            if ($target->isDestroyed())
                return;
            $calledsystem = null;
            
            if ($fireOrder->calledid != -1){
                $calledsystem = $target->getSystemById($fireOrder->calledid);
            }
            $system = $target->getHitSystem(null, $shooter, $fireOrder, $this, $location);
            if ($system == null)
                return;
    
            if ($fireOrder->calledid == -1){
                $this->doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata);
                return;
            }
            
            if($system->location == $calledsystem->location && $system === $calledsystem){
                $this->doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata);
            }else{
                // we haven't yet overkilled into another location:
                // check if there are more of the same type of systems in the same location
                // if so, target those first. To strip a ship of systems is the main benefit of
                // the point pulsar. (Implementation differs from official Bab5Wars rules. But
                // this implementation needs less clicking and user interaction.)
                foreach($target->systems as $targetSystem){
                    if(!$targetSystem->isDestroyed()
                            && $targetSystem->location == $calledsystem->location
                            && $targetSystem->name == $calledsystem->name){
                        $fireOrder->calledid = $targetSystem->id;
                        $this->damage($target, $shooter, $fireOrder, $pos, $gamedata, $damage, $location);
                        return;
                    }
                }
                $this->doDamage($target, $shooter, $system, $damage, $fireOrder, $pos, $gamedata);
            }
        }
	*/
    }

?>
