<?php
	class Fighter extends ShipSystem{

		public $location = 0;
		public $id, $armour, $maxhealth, $powerReq, $output, $name, $displayName;
		public $damage = array();
		public $outputMod = 0;
		public $boostable = false;
		public $power = array();
		public $data = array();
		public $critData = array();
		
		
		public $possibleCriticals = array();
		
			
		public $criticals = array();
		
		
		function __construct($armour, $maxhealth){
			parent::__construct($armour, $maxhealth, 0, 0 );
			
			
			
		}
			
		public function setSystemDataWindow($turn){
			parent::setSystemDataWindow($turn);			
			
		}
		
		public function testCritical($ship, $turn, $crits, $add = 0){
						
			return $crits;
			 
		}
		
		
		public function isOfflineOnTurn($turn){
		
			return false;
		
		}
		
		public function isOverloadingOnTurn($turn){
			
			return false;
		
		}
		
		 public function getArmourPos($target, $pos){
            $tf = $target->getFacingAngle();
            $shooterCompassHeading = mathlib::getCompassHeadingOfPos($this, $pos);
          
            return $this->doGetArmour($tf,  $shooterCompassHeading);
        }
        
        public function getArmour($target, $shooter){
            $tf = $target->getFacingAngle();
            $shooterCompassHeading = mathlib::getCompassHeadingOfShip($this, $shooter);
          
            return $this->doGetArmour($tf,  $shooterCompassHeading);
            
        }
        
        
        public function doGetDefenceValue($tf, $shooterCompassHeading){
            if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection(330,$tf), Mathlib::addToDirection(30,$tf) )){
               return $this->armour[0];
            }else if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection(150,$tf), Mathlib::addToDirection(210,$tf) )){
                return $this->armour[1];
            }else if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection(210,$tf), Mathlib::addToDirection(330,$tf) )){
                return $this->armour[2];
            }  else if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection(30,$tf), Mathlib::addToDirection(150,$tf) )){
                return $this->armour[3];
            } 
                
            return $this->armour[0];
        }
        

	}

?>
