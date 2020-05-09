<?php

class WeaponLoading
{
    public $loading, $extrashots, $loadedammo, $overloading, $loadingtime, $firingmode;
    
    public function __construct($loading, $extrashots, $loadedammo, $overloading, $loadingtime = 0, $firingmode = 1)
    {
        $this->loading = (int)$loading;
        $this->extrashots = (int)$extrashots;
        $this->loadedammo = (int)$loadedammo;
        $this->overloading = (int)$overloading;
        $this->loadingtime = (int)$loadingtime;
        $this->firingmode = (int)$firingmode;
    }
    
    public function toJSON()
    {
        return '"loading":{"1":"'.$this->loading.'","2":"'.$this->extrashots.'","3":"'.$this->loadedammo.'","4":"'.$this->overloading.'","5":"'.$this->loadingtime.'","6":"'.$this->firingmode.'"}';
    }

    public function toString() {
        return "loading: $this->loading extrashots: $this->extrashots loadedammo: $this->loadedammo overloading: $this->overloading loadingtime: $this->loadingtime firingmode: $this->firingmode";
    }
}

class PlayerSlot{
    public $slot, $team, $lastturn, $lastphase, $name, $points, $depx, $depy, $deptype, $depwidth, $depheight, $depavailable, $playerid, $playername;
    
    function __construct($playerid, $slot, $team, $lastturn, $lastphase, $name, $points, $depx, $depy, $deptype, $depwidth, $depheight, $depavailable, $playername){
        $this->playerid = $playerid;
        $this->team = $team;
        $this->lastturn = $lastturn;
        $this->lastphase = $lastphase;
        $this->name = $name;
        $this->slot = $slot;
        
        $this->points = $points;
        $this->depx = $depx;
        $this->depy = $depy;
        $this->deptype = $deptype;
        $this->depwidth = $depwidth;
        $this->depheight = $depheight;
        $this->depavailable = $depavailable;
        $this->playername = $playername;
    }
    
}

class PlayerSlotFromJSON extends PlayerSlot{
    
    public function __construct($json){
        $this->slot = $json["id"];
        $this->team = $json["team"];
        $this->lastturn = 0;
        $this->lastphase = -3;
        $this->name = $json["name"];
        
        $this->points = $json["points"];
        $this->depx = $json["depx"];
        $this->depy = $json["depy"];
        $this->deptype = $json["deptype"];
        $this->depwidth = $json["depwidth"];
        $this->depheight = $json["depheight"];
        $this->depavailable = $json["depavailable"];
    }
    
}

class MovementOrder{

    public $id, $type, $position, $xOffset, $yOffset, $facing, $heading, $speed, $value, $at_initiative;
    public $animating = false;
    public $animated = true;
    public $animationtics = 0;
    public $preturn;
    public $requiredThrust = array(0, 0, 0, 0, 0); //0:any, 1:front, 2:rear, 3:left, 4:right;
    public $assignedThrust = array();
    public $commit = true;
    public $turn;
    public $forced = false;
    
    
    function __construct($id, $type, OffsetCoordinate $position, $xOffset, $yOffset, $speed, $heading, $facing, $pre, $turn, $value, $at_initiative){
        $this->id = (int)$id;
        $this->position = $position;
        $this->type = $type;
        $this->facing = (int)$facing;
        $this->heading = (int)$heading;
        $this->speed = (int)$speed;
        $this->preturn = $pre;
        $this->turn = (int)$turn;
        $this->xOffset = $xOffset;
        $this->yOffset = $yOffset;
        $this->value = $value;
        $this->at_initiative = $at_initiative;

    }
    
    public function getReqThrustJSON(){
        return json_encode($this->requiredThrust);
        
    }
    
    public function getAssThrustJSON(){
        return json_encode($this->assignedThrust);
        
    }
    
    public function setReqThrustJSON($json){
        $this->requiredThrust = json_decode($json, true);
    }
    
    public function setAssThrustJSON($json){
        $this->assignedThrust = json_decode($json, true);
    }
    
    public function getCoPos(){
        return mathlib::hexCoToPixel($this->position);
    }
    
    public function getFacingAngle(){
    
        $d = $this->facing;
        if ($d == 0){
            return 0;
        }
        if ($d == 1){
            return 60;
        }
        if ($d == 2){
            return 120;
        }
        if ($d == 3){
            return 180;
        }
        if ($d == 4){
            return 240;
        }
        if ($d == 5){
            return 300;
        }
        
        return 0;
    }

}


class DamageEntry{

    public $id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed;
    public $pubnotes = "";
    public $updated = false;
    
    public $shooterid, $weaponid;//Marcin Sawicki: additional variables, usually useless, but needed to identify fire order whose ID is not known at the moment of dealing damage
	public $undestroyed;//for self-repair - add ability to restore sdestroyed system to function
    
    function __construct($id, $shipid, $gameid, $turn, $systemid, $damage, $armour, $shields, $fireorderid, $destroyed, $undestroyed, $pubnotes, $damageclass = null, $shooterid = null, $weaponid = null){
        $this->id = $id;
        $this->shipid = $shipid;
        $this->gameid = $gameid;
        $this->turn = $turn;
        $this->systemid = $systemid;
        $this->damage = $damage;
        $this->armour = $armour;
        $this->shields = $shields;
        $this->fireorderid = $fireorderid;
        $this->destroyed = $destroyed;
        $this->undestroyed = $undestroyed;
        $this->pubnotes = $pubnotes;
        $this->damageclass = $damageclass;        
        $this->shooterid = $shooterid;
        $this->weaponid = $weaponid;    

		/*do not allow negative effective values unless they're really healing!*/
		if (($damage>=0) && ($damage<$armour)) $armour=$damage; //otherwise interface will show that as negative effective damage!	
    }

}

class EWentry{
    
    public $id, $shipid, $turn, $type, $amount, $targetid;
    
    function __construct($id, $shipid, $turn, $type, $amount, $targetid){
         $this->id = $id;
         $this->shipid = $shipid;
         $this->turn = $turn;
         $this->type = $type;
         $this->amount = $amount;
         $this->targetid = $targetid;
    }
}


class FireOrder{    
    public $id, $type, $shooterid, $targetid, $calledid, $weaponid, $turn, $firingMode, $needed, $rolled, $shots, $shotshit, $intercepted, $x, $y, $damageclass;
    public $notes = "";
    public $pubnotes = "";
    public $updated = false;
    public $addToDB = false;
    public $armorIgnored = array(); //convenient place to store info about armor pierced with this shot
    public $linkedHit = null; //convenient place to store info about system hit by linked weapons
    public $chosenLocation = null; //convenient place to store info about section chosen to be hit when determining hit chance
    public $totalIntercept = 0; //total interception assigned
    public $numInterceptors = 0; //number of intercepting weapons assigned
    public $resolutionOrder = -1; //actual order in which shot was resolved
	public $priority = 0; //fire order priority, temporary only during fire resolution
    
    function __construct(
        $id,
        $type, 
        $shooterid, 
        $targetid, 
        $weaponid, 
        $calledid, 
        $turn, 
        $firingmode, 
        $needed = 0, 
        $rolled = 0, 
        $shots = 1, 
        $shotshit = 0, 
        $intercepted = 0, 
        $x, 
        $y,
        $damageclass = null,
        $resolutionOrder = -1
    ){
        $this->id = $id;
        $this->type = $type;
        $this->shooterid = $shooterid;
        $this->targetid = $targetid;
        $this->weaponid = $weaponid;
        $this->calledid = $calledid;
        $this->turn = $turn;
        $this->firingMode = $firingmode;
        $this->needed = $needed;
        $this->rolled = $rolled;
        $this->shots = $shots;
        $this->shotshit = $shotshit;
        $this->intercepted = $intercepted;
        $this->x = $x;
        $this->y = $y;
        $this->damageclass = $damageclass;
        $this->resolutionOrder = $resolutionOrder;
    }

}



class PowerManagementEntry{
    
    public $id, $shipid, $systemid, $type, $turn, $amount;
    public $updated = false;
    
    //types: 1:offline 2:boost, 3:overload
    
    function __construct($id, $shipid, $systemid, $type, $turn, $amount){
        $this->id = (int)$id;
        $this->shipid = (int)$shipid;
        $this->systemid = (int)$systemid;
        $this->type = (int)$type;
        $this->turn = (int)$turn;
        $this->amount = (int)$amount;

    }

}


class Ballistic{
    public $fireOrderId, $position, $id, $facing, $targetpos, $targetid, $shooterid, $weaponid, $shots;
        
    function __construct($id, $fireid, $position, $facing, $targetpos, $targetid, $shooterid, $weaponid, $shots){
        $this->id = (int)$id;
        $this->fireOrderId = (int)$fireid;
        $this->facing = (int)$facing;
        $this->targetid = (int)$targetid;
        $this->shooterid = (int)$shooterid;
        $this->weaponid = (int)$weaponid;
        $this->position = $position;
        $this->targetposition = $targetpos;
        $this->shots = $shots;
        
   }

}


/* additional non-standard information a particular system might need
*/
class IndividualNote{
	public $id,
		$gameid,
		$turn,
		$phase,
		$shipid,
		$systemid,
		$notekey,
		$notekey_human,
		$notevalue
	;
    
    function __construct($id, $gameid, $turn, $phase, $shipid, $systemid, $notekey, $notekey_human, $notevalue){
        $this->id = $id;
        $this->gameid = $gameid;
        $this->turn = $turn;
        $this->phase = $phase;
        $this->shipid = $shipid;
        $this->systemid = $systemid;
        $this->notekey = $notekey;
        $this->notekey_human = $notekey_human;
        $this->notevalue = $notevalue;
    }

}


?>
