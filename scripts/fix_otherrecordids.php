#!/usr/bin/env php
<?php
/**
 * Fix the other record ids
 *
 * @author Robbie Hott
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @copyright 2015 the Rector and Visitors of the University of Virginia, and
 *            the Regents of the University of California
 */
// Include the global autoloader generated by composer
include "../vendor/autoload.php";

use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;

// Set up the global log stream
$log = new StreamHandler("fix_otherids.log", Logger::WARNING);

// SNAC Postgres DB Handler
$dbu = new snac\server\database\DBUtil();

// SNAC Database Connectior
$db = new snac\server\database\DatabaseConnector();

// SNAC Postgres User Handler
$dbuser = new \snac\server\database\DBUser();
$tempUser = new \snac\data\User();
$tempUser->setUserName("system@localhost");
$user = $dbuser->readUser($tempUser);
$user->generateTemporarySession();

echo "Running DB Query\n";
$res = $db->query("select id, version, status, note from version_history where status = 'ingest cpf' order by id asc;", array());
echo "Running Through Constellations\n";
while (($row = $db->fetchRow($res)) != null) {
    $json = json_decode($row['note'], true);
    if (isset($json["maintenanceEvents"]) && is_array($json["maintenanceEvents"])) {
        foreach ($json["maintenanceEvents"] as &$event) {
            unset($event["operation"]);
        }
    }
    $constellation = $dbu->readPublishedConstellationByID($row['id'], \snac\server\database\DBUtil::$READ_MICRO_SUMMARY|\snac\server\database\DBUtil::$READ_OTHER_EXCEPT_RELATIONS);
    $json["otherRecordIDs"] = array();
    foreach ($constellation->getOtherRecordIDs() as $otherid) {
        if ($otherid->getType()->getTerm() == "MergedRecord") {
            array_push($json["otherRecordIDs"], $otherid->toArray());
        }
    }
    $db->query("update version_history set note = $3 where id = $1 and version = $2;", array($row["id"], $row["version"], json_encode($json, JSON_PRETTY_PRINT)));
    echo ".";
}
