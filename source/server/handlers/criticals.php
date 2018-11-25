<?php

class Criticals
{

    public static function setCriticals($gamedata)
    {

        $crits = array();
        //print("criticals");
        foreach ($gamedata->ships as $ship) {
            if ($ship->isDestroyed()) {
                continue;
            }

            foreach ($ship->systems as $system) {

                if ( /*$ship instanceof StarBase && */$system instanceof SubReactor) { //destroying any Subreactor, not just on base!
                    if ($system->wasDestroyedThisTurn($gamedata->turn)) {
                        //if ($system->location != 0){ ///on any location, PRIMARY too...
                        $ship->destroySection($system, $gamedata);
                        //}
                    }
                }

                if ($system->isDestroyed() && (!($system instanceof MissileLauncher))) { //missile launchers may still explode
                    continue;
                }

                if ($system->isDamagedOnTurn($gamedata->turn)) {
                    $crits = array_merge($crits, $system->testCritical($ship));
                }
            }
        }

        //print(var_dump($crits));
        return $crits;

    }

}
