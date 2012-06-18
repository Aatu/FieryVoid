<?php

class Debug
{
    
    public static function log($msg)
    {
        file_put_contents('/tmp/fieryvoid.log', $msg."\n", FILE_APPEND);
    }
    
    public static function error(Exception $e)
    {
        $msg = "\nMESSAGE: " .$e->getMessage();
        $msg .= "\nTRACE: " . $e->getTraceAsString();
        
        file_put_contents('/tmp/fieryvoid.log', $msg, FILE_APPEND);
    }
}