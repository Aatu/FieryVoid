<?php
//A strength 3 GRAVITY WELL - GravityWell (strength 1) is the base design, GRAVITY_WELL_PLAN.md R1.
class GravityWell3 extends GravityWell{

    function __construct($id, $userid, $name, $slot){
        parent::__construct($id, $userid, $name, $slot);

        $this->phpclass = "GravityWell3";
        $this->setGravityStrength(3);
        $this->variantOf = "Gravity Well (Strength 1)";
    }
}
?>
