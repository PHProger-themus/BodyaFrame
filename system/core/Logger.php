<?php

namespace system\core;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Cfg;

class Logger implements LoggerInterface {
    
    private bool $useSystem;
    
    public function __construct(bool $useSystem = false) {
        $this->useSystem = $useSystem;
    }
    
    private function processContext(&$message, array $context) {
        
        foreach ($context as $var => $value) {           
            $message = str_replace("{$var}", $value, $message);
        }
        
    }
    
    private function addLog($level, $message, array $context = array()) {
        
        if ($context != null) $this->processContext($message, $context);
       
        $time = date('[D, F d, Y H:i:s]');
        $level = strtoupper($level);
           
        $logMessage = "$time $level: $message" . PHP_EOL;
        $logMessageSystem = "$level: $message";
           
        if ($this->useSystem) error_log($logMessageSystem);
        else {
            $logFileName = HOME_DIR . "/logs/$level.log";
            $logFile = fopen($logFileName, 'a');
            fwrite($logFile, $logMessage);
            fclose($logFile);
        }
        
    }
    
    public function emergency($message, array $context = array()) {
        
        $this->addLog(LogLevel::EMERGENCY, $message, $context);
        
    }
    
    public function alert($message, array $context = array()) {
        
        $this->addLog(LogLevel::ALERT, $message, $context);
        
    }
    
    public function critical($message, array $context = array()) {
        
        $this->addLog(LogLevel::CRITICAL, $message, $context);
        
    }
    
    public function error($message, array $context = array()) {
        
        $this->addLog(LogLevel::ERROR, $message, $context);
        
    }
    
    public function warning($message, array $context = array()) {
        
        $this->addLog(LogLevel::WARNING, $message, $context);
        
    }
    
    public function notice($message, array $context = array()) {
        
        $this->addLog(LogLevel::NOTICE, $message, $context);
        
    }
    
    public function info($message, array $context = array()) {
        
        $this->addLog(LogLevel::INFO, $message, $context);
        
    }
    
    public function debug($message, array $context = array()) {
        
        $this->addLog(LogLevel::DEBUG, $message, $context);
        
    }
    
    public function log($level, $message, array $context = array()) {
        
        $this->addLog($level, $message, $context);
        
    }
    
}
