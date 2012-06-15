<?php

class Debug
{
    
    public static function log($msg)
    {
        file_put_contents('/tmp/fieryvoid.log', $msg, FILE_APPEND);
    }
    
    public static function error($msg)
    {
        file_put_contents('/tmp/fieryvoid.log', 'ERROR: ' . $msg, FILE_APPEND);
    }
}