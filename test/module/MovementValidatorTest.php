<?php

require_once '../TestBase.php';

class MovementValidatorTest extends TestBase
{

    private $startMove;

    public function setUp()
    {
        parent::setUp();
        $this->startMove = new MovementOrder(1, "end", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, 1), 0, false, 1, 0);
    }

    /**
     * @test
     */
    public function testMovementValidation()
    {
        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;

        $thruster2 = new Thruster(3, 8, 0, 2, 3);
        $thruster2->id = 2;

        $thruster3 = new ManouveringThruster(3, 8, 0, 6, 2);
        $thruster3->id = 3;

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 2;
        $ship->pivotcost = 2;
        $ship->systems = [$thruster1, $thruster2, $thruster3, new Engine(1, 10, 5, 10, 3)];

        $ship->movement = [
            //$id, $type, OffsetCoordinate $position, OffsetCoordinate $target, $facing, $rolled, $turn, $value = 0, $requiredThrust = null

            new MovementOrder(2, "speed", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, 0), 0, false, 2, 0, new RequiredThrust([
                'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 2]]],
                'requirements' => [3 => 2],
            ])),
            new MovementOrder(3, "pivot", new OffsetCoordinate(1, 0), new OffsetCoordinate(0, 0), 1, false, 2, 1, new RequiredThrust([
                'fullfilments' => [6 => [["thrusterId" => 3, "amount" => 2]]],
                'requirements' => [6 => 2],
            ])),
            new MovementOrder(4, "speed", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, -1), 1, false, 2, 1, new RequiredThrust([
                'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 1], ["thrusterId" => 2, "amount" => 1]]],
                'requirements' => [3 => 2],
            ])),
        ];

        $validator = new MovementValidator($ship, 2, $this->startMove);
        $this->assertTrue($validator->validate());

        $endMove = $validator->getNewEndMove();
        $this->assertTrue($endMove->target->equals(new OffsetCoordinate(2, 0)));
        $this->assertTrue($endMove->position->equals(new OffsetCoordinate(3, 0)));
    }

    /**
     * @test
     */
    public function testEvadeAndRoll()
    {
        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;

        $thruster2 = new Thruster(3, 8, 0, 2, 3);
        $thruster2->id = 2;

        $thruster3 = new ManouveringThruster(3, 8, 0, 6, 2);
        $thruster3->id = 3;

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 2;
        $ship->pivotcost = 2;
        $ship->evasioncost = 1;
        $ship->rollcost = 1;
        $ship->systems = [$thruster1, $thruster2, $thruster3, new Engine(1, 10, 5, 10, 3)];

        $ship->movement = [
            //$id, $type, OffsetCoordinate $position, OffsetCoordinate $target, $facing, $rolled, $turn, $value = 0, $requiredThrust = null
            new MovementOrder(2, "roll", new OffsetCoordinate(1, 0), new OffsetCoordinate(0, 0), 0, false, 2, 1, new RequiredThrust([
                'fullfilments' => [6 => [["thrusterId" => 3, "amount" => 1]]],
                'requirements' => [6 => 1],
            ])),
            new MovementOrder(3, "evade", new OffsetCoordinate(1, 0), new OffsetCoordinate(0, 0), 0, false, 2, 2, new RequiredThrust([
                'fullfilments' => [6 => [["thrusterId" => 3, "amount" => 2]]],
                'requirements' => [6 => 2],
            ])),
            new MovementOrder(4, "speed", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, 0), 0, false, 2, 0, new RequiredThrust([
                'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 2]]],
                'requirements' => [3 => 2],
            ])),
            new MovementOrder(5, "pivot", new OffsetCoordinate(1, 0), new OffsetCoordinate(0, 0), 1, false, 2, 1, new RequiredThrust([
                'fullfilments' => [6 => [["thrusterId" => 3, "amount" => 2]]],
                'requirements' => [6 => 2],
            ])),
            new MovementOrder(6, "speed", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, -1), 1, false, 2, 1, new RequiredThrust([
                'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 1], ["thrusterId" => 2, "amount" => 1]]],
                'requirements' => [3 => 2],
            ])),
        ];

        $validator = new MovementValidator($ship, 2, $this->startMove);
        $this->assertTrue($validator->validate());
    }

    /**
     * @test
     */
    public function testRollAndEvadeInWrongOrder()
    {
        $this->expectExceptionMessage("Roll movement is allowed only as the first move of turn");
        $this->expectException(MovementValidationException::class);

        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;

        $thruster2 = new Thruster(3, 8, 0, 2, 3);
        $thruster2->id = 2;

        $thruster3 = new ManouveringThruster(3, 8, 0, 6, 2);
        $thruster3->id = 3;

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 2;
        $ship->pivotcost = 2;
        $ship->evasioncost = 1;
        $ship->rollcost = 1;
        $ship->systems = [$thruster1, $thruster2, $thruster3, new Engine(1, 10, 5, 10, 3)];

        $ship->movement = [
            //$id, $type, OffsetCoordinate $position, OffsetCoordinate $target, $facing, $rolled, $turn, $value = 0, $requiredThrust = null
            new MovementOrder(3, "evade", new OffsetCoordinate(1, 0), new OffsetCoordinate(0, 0), 0, false, 2, 2, new RequiredThrust([
                'fullfilments' => [6 => [["thrusterId" => 3, "amount" => 2]]],
                'requirements' => [6 => 2],
            ])),
            new MovementOrder(2, "roll", new OffsetCoordinate(1, 0), new OffsetCoordinate(0, 0), 0, false, 2, 1, new RequiredThrust([
                'fullfilments' => [6 => [["thrusterId" => 3, "amount" => 1]]],
                'requirements' => [6 => 1],
            ])),
            new MovementOrder(4, "speed", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, 0), 0, false, 2, 0, new RequiredThrust([
                'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 2]]],
                'requirements' => [3 => 2],
            ])),
            new MovementOrder(5, "pivot", new OffsetCoordinate(1, 0), new OffsetCoordinate(0, 0), 1, false, 2, 1, new RequiredThrust([
                'fullfilments' => [6 => [["thrusterId" => 3, "amount" => 2]]],
                'requirements' => [6 => 2],
            ])),
            new MovementOrder(6, "speed", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, -1), 1, false, 2, 1, new RequiredThrust([
                'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 1], ["thrusterId" => 2, "amount" => 1]]],
                'requirements' => [3 => 2],
            ])),
        ];

        $validator = new MovementValidator($ship, 2, $this->startMove);
        $this->assertTrue($validator->validate());
    }

    /**
     * @test
     */
    public function testEvadePosition()
    {

        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;

        $thruster2 = new Thruster(3, 8, 0, 2, 3);
        $thruster2->id = 2;

        $thruster3 = new ManouveringThruster(3, 8, 0, 6, 2);
        $thruster3->id = 3;

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 2;
        $ship->pivotcost = 2;
        $ship->evasioncost = 1;
        $ship->rollcost = 1;
        $ship->systems = [$thruster1, $thruster2, $thruster3, new Engine(1, 10, 5, 10, 3)];

        $ship->movement = [
            //$id, $type, OffsetCoordinate $position, OffsetCoordinate $target, $facing, $rolled, $turn, $value = 0, $requiredThrust = null
            new MovementOrder(3, "evade", new OffsetCoordinate(1, 0), new OffsetCoordinate(0, 0), 0, false, 2, 2, new RequiredThrust([
                'fullfilments' => [6 => [["thrusterId" => 3, "amount" => 2]]],
                'requirements' => [6 => 2],
            ])),
            new MovementOrder(4, "speed", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, 0), 0, false, 2, 0, new RequiredThrust([
                'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 2]]],
                'requirements' => [3 => 2],
            ])),
            new MovementOrder(5, "pivot", new OffsetCoordinate(1, 0), new OffsetCoordinate(0, 0), 1, false, 2, 1, new RequiredThrust([
                'fullfilments' => [6 => [["thrusterId" => 3, "amount" => 2]]],
                'requirements' => [6 => 2],
            ])),
            new MovementOrder(6, "speed", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, -1), 1, false, 2, 1, new RequiredThrust([
                'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 1], ["thrusterId" => 2, "amount" => 1]]],
                'requirements' => [3 => 2],
            ])),
        ];

        $validator = new MovementValidator($ship, 2, $this->startMove);
        $this->assertTrue($validator->validate());
    }

    /**
     * @test
     */
    public function testInsufficientEnginePower()
    {
        $this->expectExceptionMessage("Insufficient engine power");
        $this->expectException(MovementValidationException::class);

        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;

        $thruster2 = new Thruster(3, 8, 0, 2, 3);
        $thruster2->id = 2;

        $thruster3 = new ManouveringThruster(3, 8, 0, 6, 2);
        $thruster3->id = 3;

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 2;
        $ship->pivotcost = 2;
        $ship->systems = [$thruster1, $thruster2, $thruster3, new Engine(1, 10, 5, 2, 3)];

        $ship->movement = [
            //$id, $type, OffsetCoordinate $position, OffsetCoordinate $target, $facing, $rolled, $turn, $value = 0, $requiredThrust = null
            new MovementOrder(2, "speed", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, 0), 0, false, 2, 0, new RequiredThrust([
                'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 2]]],
                'requirements' => [3 => 2],
            ])),
            new MovementOrder(3, "pivot", new OffsetCoordinate(1, 0), new OffsetCoordinate(0, 0), 1, false, 2, 1, new RequiredThrust([
                'fullfilments' => [6 => [["thrusterId" => 3, "amount" => 2]]],
                'requirements' => [6 => 2],
            ])),
            new MovementOrder(4, "speed", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, -1), 1, false, 2, 1, new RequiredThrust([
                'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 1], ["thrusterId" => 2, "amount" => 1]]],
                'requirements' => [3 => 2],
            ])),
        ];

        $validator = new MovementValidator($ship, 2, $this->startMove);
        $this->assertTrue($validator->validate());
    }

    /**
     * @test
     */
    public function testCriticalsFromOverChannel()
    {
        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;

        $thruster2 = new Thruster(3, 8, 0, 2, 3);
        $thruster2->id = 2;

        $thruster3 = new ManouveringThruster(3, 8, 0, 6, 2);
        $thruster3->id = 3;

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 6;
        $ship->pivotcost = 2;
        $ship->systems = [$thruster1, $thruster2, $thruster3, new Engine(1, 10, 5, 10, 3)];

        $ship->movement = [
            //$id, $type, OffsetCoordinate $position, OffsetCoordinate $target, $facing, $rolled, $turn, $value = 0, $requiredThrust = null
            new MovementOrder(2, "speed", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, 0), 0, false, 2, 0, new RequiredThrust([
                'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 4], ["thrusterId" => 2, "amount" => 2]]],
                'requirements' => [3 => 6],
            ])),
        ];

        $validator = new MovementValidator($ship, 2, $this->startMove);
        $this->assertTrue($validator->validate());

        Dice::forceForTest([20]);
        $criticals = $validator->getCriticals();
        $this->assertEquals($criticals[0]->phpclass, "HalfEfficiency");
    }

    /**
     * @test
     */
    public function testEndMoveInWrongPlace()
    {
        $this->expectExceptionMessage("Found unrecognized movement type 'end'");
        $this->expectException(MovementValidationException::class);

        $thruster1 = new Thruster(3, 8, 0, 2, 3);
        $thruster1->id = 1;

        $thruster2 = new Thruster(3, 8, 0, 2, 3);
        $thruster2->id = 2;

        $thruster3 = new ManouveringThruster(3, 8, 0, 6, 2);
        $thruster3->id = 3;

        $ship = new BaseShip(1, 1, "testship", 1);
        $ship->accelcost = 6;
        $ship->pivotcost = 2;
        $ship->systems = [$thruster1, $thruster2, $thruster3, new Engine(1, 10, 5, 10, 3)];

        $ship->movement = [
            //$id, $type, OffsetCoordinate $position, OffsetCoordinate $target, $facing, $rolled, $turn, $value = 0, $requiredThrust = null
            new MovementOrder(1, "end", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, 1), 0, false, 2, 0),
            new MovementOrder(2, "speed", new OffsetCoordinate(1, 0), new OffsetCoordinate(1, 0), 0, false, 2, 0, new RequiredThrust([
                'fullfilments' => [3 => [["thrusterId" => 1, "amount" => 4], ["thrusterId" => 2, "amount" => 2]]],
                'requirements' => [3 => 6],
            ])),
        ];

        $validator = new MovementValidator($ship, 2, $this->startMove);
        $this->assertTrue($validator->validate());
    }
}
