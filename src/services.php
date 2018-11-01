<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
require_once("config.php");
require_once('fileLogger/FileLogger.php');

use gehaxelt\fileLogger\FileLogger;

//----------------------------------------------------------------------
function clearLogs(){
	file_put_contents(__DIR__."/".log_file_path, '');	
}
//----------------------------------------------------------------------
function addLogs($log, $level){
	$logger = new FileLogger(__DIR__."/".log_file_path);

	if($level == 1){
		$logger->log($log, FileLogger::NOTICE);
	}else if($level == 2){
		$logger->log($log, FileLogger::WARNING);
	}else if($level == 3){
		$logger->log($log, FileLogger::ERROR);
	}else if($level == 4){
		$logger->log($log, FileLogger::FATAL);
	}
}
//----------------------------------------------------------------------
function removeOldLogs(){

	$maxLines = 1000;	
	$lines = file(__DIR__."/".log_file_path);
	$countOfLines = count($lines);	 

	if($countOfLines > $maxLines){
		$lines = array_slice($lines, $maxLines / 2);
		// Write to file
		$file = fopen(__DIR__."/".log_file_path, 'w');
		fwrite($file, implode('', $lines));
		fclose($file);
	}
}
//----------------------------------------------------------------------

$opc = isset($_GET["action"])?$_GET["action"]:"";

if($opc === "clear"){
	clearLogs();
	echo('{"status":"success"}');
}else if($opc === "add"){
	$log = isset($_POST["log"])?$_POST["log"]:"";
	$level = isset($_POST["level"])?$_POST["level"]:1;	

	if($log != ""){
		addLogs($log, $level);
	}else{
		$json = file_get_contents('php://input');
		$log1 = json_decode($json);
		$log = $log1->{'log'};
		$level = $log1->{'level'};	
		if($log != ""){	
			addLogs($log, $level);
		}
	}
	removeOldLogs();
	echo('{"status":"success"}');
}else if($opc === "save"){
	$entityBody = file_get_contents('php://input');
	$fecha = new DateTime();
	$logger = new FileLogger(__DIR__."/logs/crashlog".$fecha->getTimestamp().".txt");
	$logger->log($entityBody, FileLogger::WARNING);		
	echo('{"status":"success"}');
	addLogs($log, 1);
}

/*
$logger = new FileLogger(__DIR__."/".log_file_path);
$logger->log("test 1", FileLogger::NOTICE);
$logger->log("test 1", FileLogger::NOTICE);
$logger->log("test 2", FileLogger::NOTICE);
$logger->log("test 1", FileLogger::NOTICE);
$logger->log("test 2", FileLogger::NOTICE);
$logger->log("test 1", FileLogger::NOTICE);
$logger->log("test 2", FileLogger::NOTICE);
$logger->log("test 2", FileLogger::NOTICE);
*/
?>