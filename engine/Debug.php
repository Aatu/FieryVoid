<?php

class Debug
{
    
    public static function log($msg)
    {
        self::doLog($msg);
    }
    
    public static function error(Exception $e)
    {
        $msg = "\nMESSAGE: " .$e->getMessage();
        $msg .= "\nTRACE: " . $e->getTraceAsString();
        
        self::doLog($msg);
    }
    
    private static function doLog($msg)
    {
        $date = date('Y-m-d H:i:s');
        
        $msg = "[$date] $msg \n";
        file_put_contents('/tmp/fieryvoid.log', $msg, FILE_APPEND);
    }
}