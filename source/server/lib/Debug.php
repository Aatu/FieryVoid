<?php

class Debug
{
    
    public static function log($msg)
    {
        return self::doLog($msg);
    }
    
    /**
     * Log an exception or error
     * @param Throwable|Exception $e
     */
    public static function error($e)
    {
        if (!$e instanceof Throwable && !$e instanceof Exception) {
            return self::log("Debug::error called with non-exception: " . var_export($e, true));
        }

        $msg = "\nEXCEPTION: " . get_class($e);
        $msg .= "\nMESSAGE: " .$e->getMessage();
        $msg .= "\nFILE: " . $e->getFile() . " (" . $e->getLine() . ")";
        $msg .= "\nTRACE: " . $e->getTraceAsString();
        
        return self::doLog($msg);
    }
    
    private static function doLog($msg)
    {
        try {
            $date = date('Y-m-d H:i:s');
            $UID = uniqid();
            
            // Gather context
            $method = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
            $uri = $_SERVER['REQUEST_URI'] ?? 'N/A';
            $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            
            $context = "\n--- CONTEXT ---";
            $context .= "\nMETHOD: $method";
            $context .= "\nURI: $uri";
            $context .= "\nIP: $ip";
            
            if (!empty($_REQUEST)) {
                $context .= "\nREQUEST: " . json_encode($_REQUEST, JSON_PARTIAL_OUTPUT_ON_ERROR);
            }
            
            if (isset($_SESSION) && !empty($_SESSION)) {
                $context .= "\nSESSION: " . json_encode($_SESSION, JSON_PARTIAL_OUTPUT_ON_ERROR);
            }
            $context .= "\n------------\n";

            $fullMsg = "[$UID][$date] $msg$context\n";
            
            // 1. Output to System Log (Docker captures this)
            // We use a more concise format for the console to keep it readable
            $consoleMsg = "FV_DEBUG: [$UID] " . str_replace(array("\n", "\r"), " ", $msg);
            @error_log($consoleMsg);

            // 2. Output to Project Log File (Full context)
            $logDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'logs';
            if (!is_dir($logDir)) {
                @mkdir($logDir, 0777, true);
            }
            
            $logFile = $logDir . DIRECTORY_SEPARATOR . 'fieryvoid.log';
            
            if (is_writable($logDir) || (!file_exists($logFile) && is_writable($logDir)) || (file_exists($logFile) && is_writable($logFile))) {
                @file_put_contents($logFile, $fullMsg, FILE_APPEND);
            }
            
            return $UID;
        } catch (Throwable $t) {
            @error_log("Debug::doLog fatal failure: " . $t->getMessage());
            return "LOG_ERROR";
        }
    }
}