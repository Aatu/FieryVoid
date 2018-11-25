<?php
class Movement
{

    public static function isRolled($ship, $turn = false)
    {
        return (boolean) self::getLastEndMove($ship, $turn)->rolled;
    }

    public static function getEvasion($ship, $turn = false)
    {
        $evadeMove = null;

        foreach ($ship->movement as $move) {
            if ($turn !== false && $move->turn === $turn && $move->isEvade()) {
                $evadeMove = $move;
                break;
            }
        }

        if (!$evadeMove) {
            return 0;
        } else {
            return $evadeMove->value;
        }
    }

    public static function getMaxEvasion($ship, $turn = false)
    {
        $total = 0;
        foreach ($ship->getThrusters() as $thruster) {
            if ($thruster instanceof ManouveringThruster && !$thruster->isDestroyed($turn)) {
                $total += $thruster->maxEvasion;
            }
        }

        return $total;
    }

    public static function getThrustProduced($ship)
    {
        $total = 0;
        foreach ($ship->systems as $system) {
            if ($system instanceof Engine) {
                $total += $system->getOutput();
            }
        }

        return $total;
    }

    public static function getStartMove($ship)
    {
        foreach (array_reverse($ship->movement) as $move) {
            if ($move->isEnd() || $move->isDeploy() || $move->isStart()) {
                return $move;
            }
        }

        throw new Exception("Could not find deploy or end move");
    }
}
