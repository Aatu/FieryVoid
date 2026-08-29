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

/* Old version without 'waiting' variable - DK June 2025
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
*/

class PlayerSlot {
    public $slot, $team, $lastturn, $lastphase, $name, $points;
    public $depx, $depy, $deptype, $depwidth, $depheight, $depavailable;
    public $playerid, $playername, $waiting, $surrendered; // ✅ include $waiting, surrendered

    /*Chameleon Sensor Suite - how much this slot's fleet value, as THIS viewer computes it from the
      rows they can see, overstates what the fleet actually cost. Non-zero only when a disguised ship
      is wearing a simulacrum dearer than itself; see TacGamedata::setChameleonFleetValueAdjust().

      Declared with a 0 default rather than passed to the constructor deliberately: the field then
      ships on EVERY slot of EVERY game, so its presence says nothing. A field that appeared only on
      the deceiving slot would be a louder tell than the arithmetic it exists to hide.*/
    public $fleetValueAdjust = 0;

    /*REINFORCEMENTS_PLAN.md §3.6 - what this slot is still holding in hyperspace, as a COUNT and a
      POINT TOTAL and nothing else. Never classes, never names.

      Written per viewer by TacGamedata::hideHyperspaceReinforcements, which is the same sweep that
      deletes those units from $this->ships - so the OWNER and their team see the real rows and both
      of these stay 0, and everybody else sees these two numbers and no rows at all.

      Declared with a 0 default rather than passed to the constructor for exactly the reason
      $fleetValueAdjust above is: the fields then ship on EVERY slot of EVERY game, so their
      presence says nothing. A field that appeared only on a slot holding reinforcements would be a
      louder tell than the numbers it exists to blur.*/
    public $reinforcementCount = 0;
    public $reinforcementPoints = 0;

    /*REINFORCEMENTS_PLAN.md §2.3 - THE JUMP POINTS THIS SLOT IS OPENING, as [{x, y, facing}, ...].

      A list of HEXES AND FACINGS and nothing else - never which unit declared one, never how many
      ride it. The blue "Jump Point Forming" marker is public for the whole of the turn a jump point
      forms in (§2.3: "the only thing that exists during turn N is the ballistic marker at the
      declared hex"), and that is deliberately the warning an opponent gets in exchange for
      reinforcements arriving able to act.

      ⭐ IT HAS TO BE REPUBLISHED HERE because the sweep that fills it is the same one that DELETES
      the declaring ship from this viewer's payload - orders and all. The owner's own client draws
      the identical marker straight from its own fire order; this is the channel for everybody else.

      Populated only in a MASKED payload, and only from phase 2 onward: a declaration is secret
      while Initial Orders are open, which is the same rule hideSystemFireOrders enforces on the
      order itself.*/
    public $formingExits = array();

    function __construct(
        $playerid, $slot, $team, $lastturn, $lastphase, $name, $points,
        $depx, $depy, $deptype, $depwidth, $depheight, $depavailable,
        $playername, $waiting, $surrendered
    ) {
        $this->playerid = $playerid;
        $this->slot = $slot;
        $this->team = $team;
        $this->lastturn = $lastturn;
        $this->lastphase = $lastphase;
        $this->name = $name;
        $this->points = $points;
        $this->depx = $depx;
        $this->depy = $depy;
        $this->deptype = $deptype;
        $this->depwidth = $depwidth;
        $this->depheight = $depheight;
        $this->depavailable = $depavailable;
        $this->playername = $playername;
        $this->waiting = $waiting;
        $this->surrendered = $surrendered; // ✅ store new property        
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
        $this->deptype = "box";
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
    public $damageclass;
    
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
    public $rejected = false; //set by Firing::validateFireOrders for corrupt orders (stale client blueprint); submitFireorders skips these so they never persist
    public $armorIgnored = array(); //convenient place to store info about armor pierced with this shot
    public $linkedHit = null; //convenient place to store info about system hit by linked weapons
    public $chosenLocation = null; //convenient place to store info about section chosen to be hit when determining hit chance
    public $totalIntercept = 0; //total interception assigned
    public $numInterceptors = 0; //number of intercepting weapons assigned
    public $resolutionOrder = -1; //actual order in which shot was resolved
	public $priority = 0; //fire order priority, temporary only during fire resolution
	/*Chameleon Sensor Suite (D9): the called-shot id AS THE SHOOTER AIMED IT, i.e. a system id on
	  the simulacrum, kept when Firing::withdrawChameleonCalledShots() clears calledid so the call
	  cannot land on an arbitrary system of the REAL hull (which rolls the hit on its own chart
	  instead). Two things still read it: the mirrored allocation (D3) aims pass 2 with it, since it
	  is valid on the phantom by construction, and BaseShip::getCalledSystemAsAimed() resolves it on
	  the simulacrum so the called-shot to-hit maths still sees the call the shooter declared.
	  Transient and server-side only - it never reaches the client or the database, and a POST-side
	  rebuild drops it, which is correct: withdrawal happens fresh on every resolution.*/
	public $chameleonCalledId = null;
	/*Chameleon Sensor Suite (D3b, Stage 7): the SECOND to-hit threshold and its own hit tally, for a
	  shot fired at a ship the shooter still sees as somebody else. null - and therefore free - for
	  every shot in every game without one.

	    'needed' : the threshold computed off the SIMULACRUM's profile and DEW. It governs the
	               PHANTOM sheet and it is what the deceived viewer is shown, so their preview and
	               their combat log agree. $this->needed keeps the REAL threshold, which governs the
	               real hull - the disguise must never make the real ship harder to hit.
	    'hit'    : how many hits the phantom took, counted alongside $shotshit (the real count).
	    'mirror' : per-shot gate read by Weapon::mirrorChameleonDamage - false when this shot beat
	               the real threshold but not the fake one. Defaults true everywhere else, so every
	               other caller of damage() keeps the Stage 5 behaviour.

	  ONE property rather than three so an ordinary game pays the same payload cost as
	  $chameleonCalledId above: a single null. Transient - it is rebuilt at each resolution and the
	  persisted form is the CHAM: tag Weapon::fire() appends to $notes.*/
	public $chameleonFake = null;

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
        $x = 0, 
        $y =0,
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
    public $fireOrderId, $position, $id, $facing, $targetposition, $targetid, $shooterid, $weaponid, $shots;
        
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
