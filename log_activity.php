<?php
    function write_log($message) {
        $logFile = __DIR__ . '/logs/' . date('dmY') . '.log';
        $time = date('H:i:s');
        $logMessage = "[$time] $message" . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
