
public doFillSectionListWithStruct($sectionList){
               foreach($sectionList as $key => $currSection) {
                              if($currSection["boxes"] == 0){ //else does not need filling
				   $structure = $this->getStructureSystem($location);
                		   if ($structure != null && $structure->isDestroyed($turn-1))
                                            $sectionList[$key]["boxes"] = ?; //section remaining Structure
                                            $sectionList[$key]["armor"] = ?; //section Structure Armor
				   }
                              }
               }
}
 
 
public doGetSectionList(){ //has to be redefined for each class with different arcs
               //VIRTUAL outer sections: ID, arc from, arc to, profile, current structure boxes, current armor
               $sectionList = array(
                              array("id"=>1, "arcFrom"=>330, "arcTo"=>30, "profile"=>$this->$forwardDefense, "boxes"=>0, "armor"=>0),
                              array("id"=>2, "arcFrom"=>150, "arcTo"=>210, "profile"=>$this->$forwardDefense, "boxes"=>0, "armor"=>0),
                              array("id"=>3, "arcFrom"=>210, "arcTo"=>330,  "profile"=>$this->$sideDefense, "boxes"=>0, "armor"=>0),
                              array("id"=>4, "arcFrom"=>210, "arcTo"=>330,  "profile"=>$this->$sideDefense, "boxes"=>0, "armor"=>0),
               );
               $sectionList = doFillSectionListWithStruct($sectionList);
               return $sectionList;
}//doGetSectionList
 
 
 
public function doGetHitSectionAsArray($relativeBearing){ //decides which section/profile should be hit
               $sectionList = doGetSectionList();
 
            $currLocation = array("id"=>0, "arcFrom"=>0, "arcTo"=>0, "profile"=>99, "boxes"=>0, "armor"=>0);//obviously worse than any real location, even destroyed
		foreach($sectionList as $key => $currSection){
			if( mathlib::isInArc($relativeBearing,$currSection["arcFrom"],$currSection["arcTo"])){ //else not in arc
/*algorithm deciding if $currSection is better target than $currLocation*/
				$isBetter = false;
				$weightedBoxesLocation = $currLocation["boxes"]*(10+$currLocation["armor"]);
				$weightedBoxesSection = $currSection["boxes"]*(10+$currSection["armor"]);
				//if $currSection is >0 structure and $currLocation is 0
				if( ( $weightedBoxesSection > 0 ) and ($weightedBoxesLocation == 0) ) $isBetter = true;
				//if profile is no worse and weighted structure is bigger
				if( ( $weightedBoxesSection > $weightedBoxesLocation ) 
					and ( $currSection["profile"] <= $currLocation["profile"] )
				) $isBetter = true;
				//if there's at least twice Structure and at least 20 boxes of it, even if profile is bigger
				//else better profile wins, unless destroyed and current is not
				if( ( $weightedBoxesSection >= (2*$weightedBoxesLocation) ) 
					and ( $currSection["boxes"] >= 20 )
				) {$isBetter = true;} else if(
					($currSection["profile"] < $currLocation["profile"])
					and ( ( $currSection["boxes"] > 0 ) or ( $currLocation["boxes"] = 0 ) )
				) {$isBetter = true;}
				

				if($isBetter==true){
					$currLocation=$currSection;
				}
			}
		}
                
            return $currLocation;
} //doGetHitSectionAsArray


public function getRelativeBearing($pos, $shooter, $turn, $weapon){

            $tf = $this->getFacingAngle();
            $shooterCompassHeading = 0;
            
            if ( ($weapon == null) || (! $weapon->ballistic)){
                $shooterCompassHeading = mathlib::getCompassHeadingOfShip($this, $shooter);
            }else{
                $shooterCompassHeading = mathlib::getCompassHeadingOfPos($this, $pos);
            }

	$relativeBearing = Mathlib::addToDirection($tf,$shooterCompassHeading);
	if( Movement::isRolled($this) ){ //if ship is rolled, mirror relative bearing
		if( $relativeBearing <> 0 ) { //mirror of 0 is 0
			$relativeBearing = 360-$relativeBearing;
		}
	}

	return $relativeBearing;

} //getRelativeBearing



 public function getHitSection($pos, $shooter, $turn, $weapon){
	$relativeBearing = getRelativeBearing($pos, $shooter, $turn, $weapon);

	$hitSectionArray = doGetHitSectionAsArray($relativeBearing);
	$location = $hitSectionArray["id"];

//the rest without change
	if ($location != 0){
                if (!isset($this->hitChart[0])){
                    debug::log("!isset ship->hitchart[0] getHitSection");
                    if ((($this instanceof MediumShip && Dice::d(20)>17 ) || Dice::d(10)>9) && !$weapon->flashDamage){
                        $location = 0;
                    }
                }
              
                $structure = $this->getStructureSystem($location);
                if ($structure != null && $structure->isDestroyed($turn-1))
                    return 0;
        }
        
        return $location;
            
 } //getHitSection



public function doGetDefenceValue($tf, $shooterCompassHeading){
	$relativeBearing = Mathlib::addToDirection($tf,$shooterCompassHeading);
	if( Movement::isRolled($this) ){ //if ship is rolled, mirror relative bearing
		if( $relativeBearing <> 0 ) { //mirror of 0 is 0
			$relativeBearing = 360-$relativeBearing;
		}
	}

	$hitSectionArray = doGetHitSectionAsArray($relativeBearing);
	$profile = $hitSectionArray["profile"]; 
}
















