<?php
	require_once("ShipClasses.php");
	
    class FighterFlight extends BaseShip{

        public $shipSizeClass = -1; //0:Light, 1:Medium, 2:Heavy, 3:Capital, 4:Enormous
        public $imagePath = "ships/null.png";
        public $iconPath, $shipClass;
        public $systems = array();
        public $agile = true;
        public $turncost;
        public $turndelaycost = 0;
        public $accelcost = 1;
        public $rollcost = 1;
        public $pivotcost = 1;
        public $currentturndelay = 0;
        public $iniative = "N/A";
        public $iniativebonus = 0;
        public $gravitic = false;
        public $phpclass;
        public $forwardDefense, $sideDefense;
        public $destroyed = false;
        public $pointCost = 0;
        public $faction = null;
        public $flight = true;
        
        public $offensivebonus, $freethrust;
        public $jinkinglimit = 0;
        
        
        public $canvasSize = 100;

        public $fireOrders = array();
        
        //following values from DB
        public $id, $userid, $name, $campaignX, $campaignY;
        public $rolled = false;
        public $rolling = false;
        public $team;
        


        public $movement = array();
        
        function __construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement){
            $this->id = (int)$id;
            $this->userid = (int)$userid;
            $this->name = $name;
            $this->campaignX = (int)$campaignX;
            $this->campaignY = (int)$campaignY;
            $this->rolled = $rolled;
            $this->rolling = $rolling;
            $this->movement = $movement;

        }
        
        private $autoid = 1;
        
        protected function addSystem($fighter){
            
           
            $fighter->id = $this->autoid;
            $fighter->location = sizeof($this->systems);
            
            $this->autoid++;
            
            foreach ($fighter->systems as $system){
				
				$system->id  = $this->autoid;
				$this->autoid++;
				
			}
            
            $this->systems[$fighter->id] = $fighter;
            
        
        }
		
        public function getPreviousCoPos(){
            $pos = $this->getCoPos();
            
            for ($i = sizeof($this->movement)-1; $i>=0; $i--){
                $move = $this->movement[$i];
                $pPos = $move->getCoPos();
                
                if ( $pPos["x"] != $pos["x"] || $pPos["y"] != $pos["y"])
                    return $pPos;
            }
            
            return $pos;
        }
        
        public function getDEW($turn){
            
            foreach ($this->EW as $EW){
                if ($EW->type == "DEW" && $EW->turn == $turn)
                    return $EW->amount;
            }
            
            return 0;
        
        }
        
        public function getOEW($target, $turn){
        
            foreach ($this->EW as $EW){
                if ($EW->type == "OEW" && $EW->targetid == $target->id && $EW->turn == $turn)
                    return $EW->amount;
            }
            
            return 0;
        }
        
        public function getFacingAngle(){
            $movement = null;
            
            foreach ($this->movement as $move){
                $movement = $move;
            }
        
            return $movement->getFacingAngle();
        }
        
        
        
        public function doGetDefenceValue($tf, $shooterCompassHeading){
            if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection(330,$tf), Mathlib::addToDirection(30,$tf) )){
               return $this->forwardDefense;
            }else if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection(150,$tf), Mathlib::addToDirection(210,$tf) )){
                return $this->forwardDefense;
            }else if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection(210,$tf), Mathlib::addToDirection(330,$tf) )){
                return $this->sideDefense;
            }  else if (mathlib::isInArc($shooterCompassHeading, Mathlib::addToDirection(30,$tf), Mathlib::addToDirection(150,$tf) )){
                return $this->sideDefense;
            } 
                
            return $this->sideDefense;
        }
        
        public function doGetHitSection($tf, $shooterCompassHeading, $turn, $weapon){
            return 0;
        }
           
        
        
        public function getHitSection($shooter, $turn, $weapon){
            
            
            return 0;
            
        }
        
        public function getStructureSystem($location){
             return null;
        }
        
        public function getFireControlIndex(){
              return 0;
               
        }
        
        public function isDestroyed(){
        
            foreach($this->systems as $system){
                if (!$system->isDestroyed()){
                    return false;
                }
                
            }
            
            return true;
        }
        
        public function isPowerless(){
        
            return false;
        }
        
        
        public function getHitSystem($pos, $shooter, $turn, $weapon, $location = null){
        
                 //print("getHitSystem, location: $location ");
            $systems = array();
          
            foreach ($this->systems as $system){
                if (!$system->isDestroyed()){
					$systems[] = $system;
                }
                            
            } 
			
			if (sizeof($systems) == 0)
				return null;
					
			return $systems[(Dice::d(sizeof($systems)) -1)];
			
        
        }
             
    }
  

?>
