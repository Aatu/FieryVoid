<?php
//A strength 2 GRAVITY WELL - GravityWell (strength 1) is the base design, GRAVITY_WELL_PLAN.md R1.
class GravityWell2 extends GravityWell{

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name, $slot);

        $this->phpclass = "GravityWell2";
        $this->setGravityStrength(2);
        $this->variantOf = "Gravity Well (Strength 1)";
    }
}
?>
