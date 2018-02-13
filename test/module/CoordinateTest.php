<?php
require_once('../TestBase.php');

class CoordinateTest extends TestBase
{
    /**
     * @test
     */
    public function testOffsetToCube()
    {
        $offset = new OffsetCoordinate(15, -17);
        $cube = $offset->toCube();
        $this->assertEquals($cube->x, 23);
        $this->assertEquals($cube->y, -6);
        $this->assertEquals($cube->z, -17);
    }

    public function testCubeCoordinateMoveToDirection()
    {
        $offset = new OffsetCoordinate(15, -17);
        $displaced = $offset->moveToDirection(2, 5);
        $this->assertEquals($displaced->q, 12);
        $this->assertEquals($displaced->r, -22);
    }


}