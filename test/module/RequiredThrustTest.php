<?php

require_once '../TestBase.php';

class RequiredThrustTest extends TestBase
{
    /**
     * @test
     */
    public function testMovementValidation()
    {
        $required = new RequiredThrust([
            'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 2], ["thrusterId" => 2, "amount" => 2]]],
            'requirements' => [3 => 4],
        ]);

        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;

        $thruster2 = new Thruster(3, 8, 0, 2, 3);
        $thruster2->id = 2;

        $thrusters = [
            $thruster1, $thruster2,
        ];

        $move = new MovementOrder(1, "speed", new OffsetCoordinate(0, 0), new OffsetCoordinate(1, 0), 0, false, 1, 0);

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 4;

        $required->setThrusters($thrusters);
        $this->assertTrue($required->validateRequirementsAreCorrect($ship, $move));
        $this->assertEquals($required->getThrustChanneledBy($thruster1), 2);
        $this->assertEquals($required->getThrustChanneledBy($thruster2), 2);
        $this->assertTrue($required->validatePaid());
    }

    /**
     * @test
     */
    public function testMovementValidationMoveNotPaid()
    {
        $this->expectException(MovementValidationException::class);

        $required = new RequiredThrust([
            'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 2], ["thrusterId" => 2, "amount" => 1]]],
            'requirements' => [3 => 4],
        ]);

        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;

        $thruster2 = new Thruster(3, 8, 0, 2, 3);
        $thruster2->id = 2;

        $thrusters = [
            $thruster1, $thruster2,
        ];

        $move = new MovementOrder(1, "speed", new OffsetCoordinate(0, 0), new OffsetCoordinate(1, 0), 0, false, 1, 0);

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 4;

        $required->setThrusters($thrusters);
        $this->assertTrue($required->validateRequirementsAreCorrect($ship, $move));
        $this->assertEquals($required->getThrustChanneledBy($thruster1), 2);
        $this->assertEquals($required->getThrustChanneledBy($thruster2), 1);
        $this->assertFalse($required->validatePaid());
    }

    /**
     * @test
     */
    public function testMovementValidationNonExistantThruster()
    {
        $this->expectException(MovementValidationException::class);

        $required = new RequiredThrust([
            'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 2], ["thrusterId" => 3, "amount" => 2]]],
            'requirements' => [3 => 4],
        ]);

        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;

        $thruster2 = new Thruster(3, 8, 0, 2, 3);
        $thruster2->id = 2;

        $thrusters = [
            $thruster1, $thruster2,
        ];

        $move = new MovementOrder(1, "speed", new OffsetCoordinate(0, 0), new OffsetCoordinate(1, 0), 0, false, 1, 0);

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 4;

        $required->setThrusters($thrusters);
        $this->assertTrue($required->validateRequirementsAreCorrect($ship, $move));
        $this->assertEquals($required->getThrustChanneledBy($thruster1), 2);
        $this->assertEquals($required->getThrustChanneledBy($thruster2), 1);
        $this->assertFalse($required->validatePaid());
    }

    /**
     * @test
     */
    public function testMovementValidationThrusterDestroyed()
    {
        $this->expectException(MovementValidationException::class);

        $required = new RequiredThrust([
            'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 2], ["thrusterId" => 2, "amount" => 2]]],
            'requirements' => [3 => 4],
        ]);

        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;
        $thruster1->damage[] = new DamageEntry(1, 1, 1, 1, 1, 999999, 1, 0, 1, true, '');

        $thruster2 = new Thruster(3, 8, 0, 2, 3);
        $thruster2->id = 2;

        $thrusters = [
            $thruster1, $thruster2,
        ];

        $move = new MovementOrder(1, "speed", new OffsetCoordinate(0, 0), new OffsetCoordinate(1, 0), 0, false, 1, 0);

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 4;

        $required->setThrusters($thrusters);
    }

    /**
     * @test
     */
    public function testMovementValidationMoveRequirementIsWrong()
    {
        $this->expectException(MovementValidationException::class);

        $required = new RequiredThrust([
            'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 2], ["thrusterId" => 2, "amount" => 2]]],
            'requirements' => [3 => 2],
        ]);

        $move = new MovementOrder(1, "speed", new OffsetCoordinate(0, 0), new OffsetCoordinate(1, 0), 0, false, 1, 0);

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 4;

        $this->assertTrue($required->validateRequirementsAreCorrect($ship, $move));
    }

    /**
     * @test
     */
    public function testMovementValidationMoveRequirementIsWrong2()
    {
        $this->expectException(MovementValidationException::class);

        $required = new RequiredThrust([
            'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 2], ["thrusterId" => 2, "amount" => 2]]],
            'requirements' => [6 => 4],
        ]);

        $move = new MovementOrder(1, "speed", new OffsetCoordinate(0, 0), new OffsetCoordinate(1, 0), 0, false, 1, 0);

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 4;

        $this->assertTrue($required->validateRequirementsAreCorrect($ship, $move));
    }

    /**
     * @test
     */
    public function testMovementValidationTryingToPayWithWrongThruster()
    {
        $this->expectException(MovementValidationException::class);

        $required = new RequiredThrust([
            'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 2], ["thrusterId" => 2, "amount" => 2]]],
            'requirements' => [3 => 4],
        ]);

        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;

        $thruster2 = new Thruster(3, 8, 0, 2, [1, 2]);
        $thruster2->id = 2;

        $thrusters = [
            $thruster1, $thruster2,
        ];

        $move = new MovementOrder(1, "speed", new OffsetCoordinate(0, 0), new OffsetCoordinate(1, 0), 0, false, 1, 0);

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 4;

        $required->setThrusters($thrusters);
    }

}
