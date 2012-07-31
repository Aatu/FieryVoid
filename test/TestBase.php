<?php

class TestBase extends PHPUnit_Framework_TestCase
{
    protected static function requireSources()
    {
        $baseDir = dirname(__DIR__) . '/source/';
        $currentDir = '';
        foreach(func_get_args() as $name)
        {
            $cleanName = preg_replace('/[^\w.-]+/', '/', $name);
            if (is_dir("$baseDir/$cleanName"))
            {
                $currentDir = "$cleanName/";
                continue;
            }
            if (is_dir("$baseDir/$currentDir/$cleanName"))
            {
                $currentDir = "$currentDir/$cleanName/";
                continue;
            }

            $fullname = $baseDir . $currentDir . $cleanName;

            if (file_exists("$fullname.php"))
            {
                $fullname .= '.php';
            }

            require_once $fullname;
        }
    }
}