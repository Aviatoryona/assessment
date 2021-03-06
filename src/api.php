<?php
//
$time_start = microtime(true);

//include 'logger/Logger.php';
require_once 'estimator.php';


//Logger::configure('config.xml');
//$log = Logger::getLogger('myLogger');
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);
$path = explode("/", $uri);
$last = end($path);

$dt = test();
$res = null;
if ($last == 'xml') {
    $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><root></root>");
    array_to_xml($dt, $xml);
    header('Content-Type: application/xml; charset=utf-8');
    $res = ($xml->asXML());
} else {
    $res = json_encode($dt);
}

print($res);
$time_end = microtime(true);
$exec_time = $time_end - $time_start;
$logmsg = time()."\t\t". $_SERVER['REQUEST_URI'] ."\t\t". 'done in ' . $exec_time . " seconds\r\n";
//$log->log(LoggerLevel::getLevelOff(),$logmsg);
file_put_contents('../logs/log.txt',$logmsg,FILE_APPEND);
