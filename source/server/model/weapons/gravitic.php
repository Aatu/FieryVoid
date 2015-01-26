<?php

    class Gravitic extends Weapon{
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Gravitic";
            $this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }
    }
    
    class GravitonPulsar extends Pulse
    {
        public $name = "gravitonPulsar";
        public $displayName = "Graviton Pulsar";
        public $animation = "trail";
        public $trailColor = array(99, 255, 00);
        public $animationColor = array(99, 255, 00);
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $animationExplosionScale = 0.15;
        public $boostable = true;
        public $boostEfficiency = 2;
        public $maxBoostLevel = 2;
        public $loadingtime = 1;
        public $maxpulses = 3;
        public $priority = 5;
		
        public $rangePenalty = 1;
        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals 
        public $intercept = 1;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            // Keep this consistent with the gravitic.js implementation.
            // Yeah, I know: dirty.
            $this->data["Weapon type"] = "Pulse";
            $this->data["Damage type"] = "Standard";
            $this->data["Grouping range"] = $this->grouping + "%";
            $this->data["Max pulses"] = $this->getMaxPulses($turn);
            $this->data["REMARK"] = "Max. power might cause<br> crits on this system";
            $this->defaultShots = $this->getMaxPulses($turn);
            $this->normalload = $this->loadingtime;
        
            $this->maxpulses = $this->getMaxPulses($turn);

            $this->setTimes();
            
            switch($this->getBoostLevel($turn)){
                case 0:
                    $this->data["Pulses"] = '1-2';
                    break;
                case 1:
                    $this->data["Pulses"] = '1-3';
                    break;
                case 2:
                    $this->data["Pulses"] = '1-3';
                    break;
            }            

            parent::setSystemDataWindow($turn);
        }
        
        public function getLoadingTime(){
            if(!(TacGamedata::$currentPhase == 1 || ($this->turnsloaded < $this->loadingtime ))){
                // In any other case, check the current boost.
                return 1 + $this->getBoostLevel(TacGamedata::$currentTurn);
            }
            else{
                return $this->loadingtime;
            }
        }

        public function getTurnsloaded(){
            if(!(TacGamedata::$currentPhase == 1 || ($this->turnsloaded < $this->loadingtime ))){
                // In any other case, check the current boost.
                return 1 + $this->getBoostLevel(TacGamedata::$currentTurn);
            }
            else{
                return $this->turnsloaded;
            }
        }
        
        public function setTimes(){
            if(!(TacGamedata::$currentPhase == 1 || ($this->turnsloaded < $this->loadingtime ))){
                // In any other case, check the current boost.
                $this->loadingtime = 1 + $this->getBoostLevel(TacGamedata::$currentTurn);
                $this->turnsloaded = 1 + $this->getBoostLevel(TacGamedata::$currentTurn);
                $this->normalload = 1 + $this->getBoostLevel(TacGamedata::$currentTurn);
            }
        }
        
        protected function getPulses($turn)
        {
            switch($this->getBoostLevel($turn)){
                case 0:
                    return Dice::d(2);
                    break;
                case 1:
                    return (Dice::d(3)+1);
                    break;
                case 2:
                    return (Dice::d(3)+2);
                    break;
            }            
        }

//        public function getIntercept($gamedata, $fireOrder){
//            $this->intercept = $this->getInterceptRating($gamedata->turn);
//            
//            parent::getIntercept($gamedata, $fireOrder);
//        }
        
        public function fire($gamedata, $fireOrder){
            $this->maxpulses = $this->getMaxPulses($gamedata->turn);
            $this->setTimes();
            
            $crits = array();
            
            /* If fully boosted: test for possible crit. */
            if($this->getBoostLevel($gamedata->turn) === $this->maxBoostLevel){
                $shooter = $gamedata->getShipById($fireOrder->shooterid);
                $crits = $this->testCritical($shooter, $gamedata, $crits);
//                $this->setCriticals($crits, $gamedata->turn);
            }
            
            parent::fire($gamedata, $fireOrder);
        }

        public function getNormalLoad(){
            return $this->loadingtime + $this->maxBoostLevel;
        }
        
        private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn)
                            continue;

                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
            }

            return $boostLevel;
        }

        private function getMaxPulses($turn){
            return 3 + $this->getBoostLevel($turn);
        }

        public function getInterceptRating($turn){
            return 1 + $this->getBoostLevel($turn);            
        }
        
        public function getDamage($fireOrder){        return 10;   }
        public function setMinDamage(){     $this->minDamage = 10 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 10 - $this->dp;      }
    }
    
class GraviticBolt extends Gravitic
    {
        public $name = "graviticBolt";
        public $displayName = "Gravitic Bolt";
        public $animation = "trail";
        public $trailColor = array(99, 255, 00);
        public $animationColor = array(99, 255, 00);
        public $projectilespeed = 12;
        public $animationWidth = 3;
        public $animationExplosionScale = 0.20;
        public $boostable = true;
        public $boostEfficiency = 2;
        public $maxBoostLevel = 2;
        public $loadingtime = 1;
        public $curDamage = 9;
        public $priority = 7;
		
        public $rangePenalty = 1;
        public $fireControl = array(4, 2, 2); // fighters, <mediums, <capitals 
        public $intercept = 1;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            // Keep this consistent with the gravitic.js implementation.
            // Yeah, I know: dirty.
            $this->data["Weapon type"] = "Gravitic";
            $this->data["Damage type"] = "Standard";
        
            switch($this->getBoostLevel($turn)){
                case 0:
                    $this->data["Damage"] = '9';
                    break;
                case 1:
                    $this->data["Damage"] = '12';
                    break;
                case 2:
                    $this->data["Damage"] = '15';
                    break;
                default:
                    $this->data["Damage"] = '9';
                    break;
            }            
            
            $this->curDamage = $this->getCurDamage($turn);
            
            parent::setSystemDataWindow($turn);
        }

        private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn)
                            continue;

                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
            }

            return $boostLevel;
        }
//        private function getBoostLevel($turn){
//            $boostLevel = 0;
//            
//            foreach($this->power as $i){
//                if($i->turn === $turn){
//                    $boostLevel += $i->amount;
//                }
//            }
//            
//            return $boostLevel;
//        }
        
        private function getCurDamage($turn){
            $dam = 9;
            
            switch($this->getBoostLevel($turn)){
                case 1:
                    $dam = 12;
                    break;
                case 2:
                    $dam = 15;
                    break;
                default:
                    break;
            }            
            
            return $dam;
        }

        public function getInterceptRating($turn){
            return 1 + $this->getBoostLevel($turn);            
        }
        
        public function getNormalLoad(){
            return $this->loadingtime + $this->maxBoostLevel;
        }
        
        public function getLoadingTime(){
            if(!(TacGamedata::$currentPhase == 1 || ($this->turnsloaded < $this->loadingtime ))){
                // In any other case, check the current boost.
                return 1 + $this->getBoostLevel(TacGamedata::$currentTurn);
            }
            else{
                return $this->loadingtime;
            }
        }

        public function getTurnsloaded(){
            if(!(TacGamedata::$currentPhase == 1 || ($this->turnsloaded < $this->loadingtime ))){
                // In any other case, check the current boost.
                return 1 + $this->getBoostLevel(TacGamedata::$currentTurn);
            }
            else{
                return $this->turnsloaded;
            }
        }
        
        public function fire($gamedata, $fireOrder){
            $this->setTimes();
            
            parent::fire($gamedata, $fireOrder);
        }

        public function setTimes(){
            if(!(TacGamedata::$currentPhase == 1 || ($this->turnsloaded < $this->loadingtime ))){
                // In any other case, check the current boost.
                $this->loadingtime = 1 + $this->getBoostLevel(TacGamedata::$currentTurn);
                $this->turnsloaded = 1 + $this->getBoostLevel(TacGamedata::$currentTurn);
                $this->normalload = 1 + $this->getBoostLevel(TacGamedata::$currentTurn);
            }
        }
        
        public function getDamage($fireOrder){        return $this->getCurDamage($fireOrder->turn);   }
        public function setMinDamage(){  $this->minDamage = $this->curDamage - $this->dp;      }
        public function setMaxDamage(){  $this->maxDamage = $this->curDamage - $this->dp;      }
    }
    
    class GravitonBeam extends Raking{
        public $name = "gravitonBeam";
        public $displayName = "Graviton Beam";
        public $animation = "laser";
        public $animationColor = array(99, 255, 00);
        public $animationWidth = 4;
        public $animationWidth2 = 0.4;
        
        public $loadingtime = 4;
        public $damageType = "raking";
        public $raking = 10;
        
        public $rangePenalty = 0.25;
        public $fireControl = array(-5, 2, 3); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Gravitic";
            $this->data["Damage type"] = "Raking";
            
            parent::setSystemDataWindow($turn);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 5)+12;   }
        public function setMinDamage(){   return  $this->minDamage = 17 - $this->dp;      }
        public function setMaxDamage(){   return  $this->maxDamage = 62 - $this->dp;      }
    }

    class GraviticCannon extends Gravitic
    {
        public $name = "graviticCannon";
        public $displayName = "Gravitic Cannon";
        public $animation = "trail";
        public $trailColor = array(99, 255, 00);
        public $animationColor = array(99, 255, 00);
        public $projectilespeed = 13;
        public $animationWidth = 2;
        public $animationExplosionScale = 0.15;
        public $loadingtime = 1;

        public $priority = 7;

		
        public $rangePenalty = 0.33;
        public $fireControl = array(-1, 2, 2); // fighters, <mediums, <capitals 
        public $intercept = 1;
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }
        
        public function getDamage($fireOrder){        return Dice::d(10)+6;;   }
        public function setMinDamage(){  $this->minDamage = 7 - $this->dp;      }
        public function setMaxDamage(){  $this->maxDamage = 16 - $this->dp;      }
    }

    class LightGraviticBolt extends LinkedWeapon{

        public $trailColor = array(99, 255, 00);

        public $name = "lightGraviticBolt";
        public $displayName = "Light Gravitic Bolt";
        public $iconPath = "lightGraviticBolt.png";
        public $animation = "trail";
        public $animationColor = array(99, 255, 00);
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


        function __construct($startArc, $endArc, $damagebonus, $shots = 2){
            $this->shots = $shots;
            $this->defaultShots = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Gravitic";
            $this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return 7;   }
        public function setMinDamage(){     $this->minDamage = 7 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 7 - $this->dp;      }

    }

    class UltraLightGraviticBolt extends LinkedWeapon{

        public $trailColor = array(99, 255, 00);

        public $name = "ultraLightGraviticBolt";
        public $displayName = "Ultra Light Gravitic Bolt";
        public $iconPath = "lightGraviticBolt.png";
        public $animation = "trail";
        public $animationColor = array(99, 255, 00);
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


        function __construct($startArc, $endArc, $damagebonus, $shots = 2){
            $this->shots = $shots;
            $this->defaultShots = $shots;
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Gravitic";
            $this->data["Damage type"] = "Standard";

            parent::setSystemDataWindow($turn);
        }

        public function getDamage($fireOrder){        return 5;   }
        public function setMinDamage(){     $this->minDamage = 5 - $this->dp;      }
        public function setMaxDamage(){     $this->maxDamage = 5 - $this->dp;      }

    }

    class LightGravitonBeam extends Weapon{
        public $name = "lightGravitonBeam";
        public $displayName = "Light Graviton Beam";
        public $animation = "laser";
        public $animationColor = array(99, 255, 00);
        public $animationWidth = 1;
        public $animationWidth2 = 0;
        
        public $loadingtime = 3;
        public $damageType = "raking";
        public $raking = 10;
        public $exclusive = true;
        
        public $rangePenalty = 1;
        public $fireControl = array(-5, 0, 0); // fighters, <mediums, <capitals 
 
        function __construct($startArc, $endArc, $damagebonus){
            
            parent::__construct(0, 1, 0, $startArc, $endArc);
        }
        
        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Gravitic";
            $this->data["Damage type"] = "Standard";
            
            parent::setSystemDataWindow($turn);
        }
        
        public function getDamage($fireOrder){        return Dice::d(6, 5);   }
        public function setMinDamage(){   return  $this->minDamage = 5 - $this->dp;      }
        public function setMaxDamage(){   return  $this->maxDamage = 30 - $this->dp;      }
    }
    
    class GraviticLance extends DualWeapon{

    public $priority = 7;

	public $firingModes = array( 
		1 => "Lance",
		2 => "Beams"
	);
	
        public $loadingtime = 4;
        public $name = "GraviticLance";
	public $displayName = "Gravitic Lance";
        	
	public function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
        
            $lance = new GravLance($armour, $maxhealth, 8, $startArc, $endArc);
            $lance->dualWeapon = true;
            $lance->parentSystem = $this;
            $beams = new DuoGravitonBeam($armour, $maxhealth, 8, $startArc, $endArc);
            $beams->dualWeapon = true;
            $beams->parentSystem = $this;
        
            $weapons = array(
                1 => $lance,
                2 => $beams
            );
		
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $weapons);
        }
        
/*        public function setLoading( $loading )
        {
            Debug::log("Enter gravlance setLoading");
            if (!$loading){
                Debug::log("Exit gravlance setLoading: nothing");
                return;
            }

            foreach ($this->weapons as $i=>$weapon){

                $weapon->setLoading($loading);
            }
        }*/
    }
    
    // Don't use the GravLance. It's only here for the GraviticLance class!!
    class GravLance extends Raking{
        public $name = "graviticLance";
        public $displayName = "Gravitic Lance";
        public $animation = "laser";
        public $animationColor = array(99, 255, 00);
        public $animationWidth = 5;
        public $animationWidth2 = 0.5;

        public $firingModes = array( 1 => "Sustained");

        
        public $loadingtime = 4;
        // Set overloading and overloadturns to have GravLance ready for
        // sustained fire in turn 1
        public $overloadable = true;
        public $alwaysoverloading = true;
        public $extraoverloadshots = 2;
        public $damageType = "raking";
        public $raking = 10;
        
        public $rangePenalty = 0.2;
        public $fireControl = array(-5, 2, 3); // fighters, <mediums, <capitals 
    
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){

            $this->data["Weapon type"] = "Gravitic";
            $this->data["Damage type"] = "Raking";
            $this->data["REMARK"] = "This weapon is always in<br>sustained mode.";
            
            parent::setSystemDataWindow($turn);
        }
        
        public function isOverloadingOnTurn($turn = null){
            return true;
        }
        
        public function getDamage($fireOrder){        return Dice::d(10, 6)+24;   }
        public function setMinDamage(){   return  $this->minDamage = 30 - $this->dp;      }
        public function setMaxDamage(){   return  $this->maxDamage = 84 - $this->dp;      }
    }
    
    class DuoGravitonBeam extends DuoWeapon{
    
        public $name = "duoGravitonBeam";
	public $displayName = "Graviton Beams";
        
        // Just for testing!
        public $loadingtime = 4;
	
	public function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc) {
            $beam1 = new GravitonBeam($armour, $maxhealth, 0, $startArc, $endArc);
            //$beam1->duoWeapon = true;
            $beam1->parentSystem = $this;
            $beam2 = new GravitonBeam($armour, $maxhealth, 0, $startArc, $endArc);
            //$beam2->duoWeapon = true;
            $beam2->parentSystem = $this;
            
            $weapons = array(
                1 => $beam1,
                2 => $beam2
            );
 
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc, $weapons);
        }
        
        
    }

    class GraviticCutter extends Raking
    {
        public $name = "graviticCutter";
        public $displayName = "Gravitic Cutter";
        public $animation = "laser";
        public $animationColor = array(99, 255, 00);
        public $animationWidth = 2;
        public $animationWidth2 = 0.2;
        
        public $damageType = "raking";
        public $raking = 6;

        public $boostable = true;
        public $boostEfficiency = 5;
        public $maxBoostLevel = 1;
        public $loadingtime = 2;
		
        public $rangePenalty = 0.5;
        public $fireControl = array(-4, 2, 4); // fighters, <mediums, <capitals 
        
        function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc)
        {
            parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
        }

        public function setSystemDataWindow($turn){
            // Keep this consistent with the gravitic.js implementation.
            // Yeah, I know: dirty.
            $this->data["Weapon type"] = "Raking";
            $this->data["REMARK"] = "This weapon is always in<br>sustained mode.";
            $this->setTimes();
//            $this->normalload = $this->loadingtime;
            
            if($this->getBoostLevel($turn)==0){
                $this->raking = 6;
            }
            else{
                $this->raking = 8;
            }
            
            parent::setSystemDataWindow($turn);
        }
        
        public function getLoadingTime(){
            if(!(TacGamedata::$currentPhase == 1 || ($this->turnsloaded < $this->loadingtime ))){
                // In any other case, check the current boost.
                return 2 + $this->getBoostLevel(TacGamedata::$currentTurn);
            }
            else{
                return $this->loadingtime;
            }
        }

        public function getTurnsloaded(){
            if(!(TacGamedata::$currentPhase == 1 || ($this->turnsloaded < $this->loadingtime ))){
                // In any other case, check the current boost.
                return 2 + $this->getBoostLevel(TacGamedata::$currentTurn);
            }
            else{
                return $this->turnsloaded;
            }
        }
        
        public function setTimes(){
            if(!(TacGamedata::$currentPhase == 1 || ($this->turnsloaded < $this->loadingtime ))){
                // In any other case, check the current boost.
                $this->loadingtime = 2 + $this->getBoostLevel(TacGamedata::$currentTurn);
                $this->turnsloaded = 2 + $this->getBoostLevel(TacGamedata::$currentTurn);
                $this->normalload = 2 + $this->getBoostLevel(TacGamedata::$currentTurn);
            }
        }
        
        public function fire($gamedata, $fireOrder){
            $this->setTimes();
            if($this->getBoostLevel($fireOrder->turn)==0){
                $this->raking = 6;
            }
            else{
                $this->raking = 8;
            }
            
            parent::fire($gamedata, $fireOrder);
        }

        public function getNormalLoad(){
            return $this->loadingtime + $this->maxBoostLevel;
        }
        
        private function getBoostLevel($turn){
            $boostLevel = 0;
            foreach ($this->power as $i){
                    if ($i->turn != $turn)
                            continue;

                    if ($i->type == 2){
                            $boostLevel += $i->amount;
                    }
            }

            return $boostLevel;
        }

        public function getDamage($fireOrder){
            if($this->getBoostLevel($fireOrder->turn)==0){
                $this->raking = 6;
                return (Dice::d(10, 2)+8);
            }
            else{
                $this->raking = 8;
                return (Dice::d(10, 3)+10);
            }
        }
        
        public function setMinDamage(){
            if($this->getBoostLevel(TacGameData::$currentTurn)==0){
                $this->minDamage = 10 - (2*$this->dp);
            }
            else{
                $this->minDamage = 13 - (2*$this->dp);
            }
        }
        
        public function setMaxDamage(){
            if($this->getBoostLevel(TacGameData::$currentTurn)==0){
                $this->maxDamage = 28 - (2*$this->dp);
            }
            else{
                $this->maxDamage = 40 - (2*$this->dp);
            }
        }
    }
    
?>
