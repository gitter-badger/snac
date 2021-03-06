<?php
/**
 * Landing page of public api interface
 *
 * Creates an instance of the Rest class and runs it
 *
 * @author Robbie Hott
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @copyright 2015 the Rector and Visitors of the University of Virginia, and
 *            the Regents of the University of California
 */

/**
 * Load and instantiate the rest api
 */
include ("../../../vendor/autoload.php");

// Namespace shortcuts
use \snac\client\rest\Rest as Rest;
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;

// Set up the global log stream
$loglevel = Logger::WARNING;
if (\snac\Config::$DEBUG_MODE) {
    $loglevel = Logger::DEBUG;
}
$log = new StreamHandler(\snac\Config::$LOG_DIR . \snac\Config::$REST_LOGFILE, $loglevel);

try {
    // Get the request body for processing
    $input = file_get_contents("php://input");
    
    if ($input == null) {
        throw new \snac\exceptions\SNACInputException("No input given to the server");
    }
    
    // Parse the JSON input
    $jsonInput = json_decode($input, true);
    if ($jsonInput == null) {
        throw new \snac\exceptions\SNACInputException("Could not parse input");
    }
    
    // Instantiate and run the server
    $server = new Rest($jsonInput);
    $server->run();
    
    // Return the content type and output of the server
    foreach ($server->getResponseHeaders() as $header)
        header($header);
    echo $server->getResponse();
} catch (Exception $e) {
    header("Content-Type: application/json");
    die($e);
}

// Exit
exit();
