<?php

class Debug
{
    
    public static function log($msg)
    {
        return self::doLog($msg);
    }
    
    public static function error(Exception $e)
    {
        $msg = "\nMESSAGE: " .$e->getMessage();
        $msg .= "\nTRACE: " . $e->getTraceAsString();
        
        return self::doLog($msg);
    }
    
    private static function doLog($msg)
    {
        $date = date('Y-m-d H:i:s');
       $UID = uniqid();
        $msg = "[$UID][$date] $msg \n";
      //  file_put_contents('C:\log/fieryvoid.log', $msg, FILE_APPEND);
        file_put_contents('/tmp/fieryvoid.log', $msg, FILE_APPEND);
        return $UID;
    }
}