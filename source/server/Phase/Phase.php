<?php

interface Phase {

    public function advance(TacGamedata $gameData, DBManager $dbManager);

    public function process(TacGamedata $gameData, DBManager $dbManager, Array $ships);

}