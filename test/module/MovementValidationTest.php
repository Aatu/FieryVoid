<?php

require_once '../TestBase.php';

class MovementValidationTest extends TestBase
{
    /**
     * @test
     */
    public function testMovementValidation()
    {
        $required = new RequiredThrust([
            'fullfilments' => [["thrusterId" => 1, "amount" => 2], ["thrusterId" => 2, "amount" => 1]],
            'requirements' => [3 => 4],
        ]);

        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;

        $thruster2 = new Thruster(3, 8, 0, 2, 3);
        $thruster2->id = 1;

        $thrusters = [
            $thruster1, $thruster2,
        ];

        $move = new MovementOrder(1, "speed", new OffsetCoordinate(0, 0), new OffsetCoordinate(1, 0), 0, false, 1, 0);

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 4;

        $this->assertTrue($required->validateRequirementsAreCorrect($ship, $move));
    }
}
