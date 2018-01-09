<?php

ini_set('display_errors',1);
error_reporting(E_ALL);
require_once dirname(__DIR__) . '/source/autoload.php';
session_start();
require_once dirname(__DIR__) . '/source/server/varconfig.php' ;


use PHPUnit\Framework\TestCase;

class TestBase extends TestCase
{
    private static $dbManager = null;

    protected function getDatabase() {
        global $database_name;
        global $database_user;
        global $database_password;
        if (self::$dbManager == null)
            self::$dbManager = new DBManager("localhost", 3306, $database_name, $database_user, $database_password, true);

        return self::$dbManager;
    }

    public function setUp() {
        parent::setUp();
        Manager::setDBManager($this->getDatabase());
        $this->getDatabase()->startTransaction();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->getDatabase()->endTransaction(true);
    }
}