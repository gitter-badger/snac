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

echo "Running VH DB Query\n";
$res = $db->query("select id, version, status, note from version_history where status = 'ingest cpf' order by id asc;", array());
echo "Running OtherRecordID DB Query\n";
$res2 = $db->query("select id, ic_id, version, text, uri from otherid where type = 28224 order by ic_id, id asc;", array());

echo "Building Lookup Table\n";
$lookup = array();
while (($row = $db->fetchRow($res2)) != null) {
    if (!isset($lookup[$row['ic_id']]))
        $lookup[$row['ic_id']] = array();
    if (isset($lookup[$row['ic_id']][$row['id']])) {
        echo "Serious Problem with the data. ID found twice.\n";
        print_r($row);
        die();
    }
    $lookup[$row['ic_id']][$row['id']] = array(
        "uri" => $row["uri"],
        "id" => $row["id"],
        "version" => $row["version"]
    );
}

$staticData = [
    "dataType" => "SameAs",
    "type" => [
        "id" => 28224,
        "term" => "MergedRecord",
        "uri" => "http://socialarchive.iath.virginia.edu/control/term#MergedRecord"
    ]
];

echo "Running Through Constellations\n";
$i = 0;
while (($row = $db->fetchRow($res)) != null) {
    $json = json_decode($row['note'], true);
    if (isset($json["maintenanceEvents"]) && is_array($json["maintenanceEvents"])) {
        foreach ($json["maintenanceEvents"] as &$event) {
            unset($event["operation"]);
        }
    }
    
    if (isset($lookup[$row['id']])) {
        $json["otherRecordIDs"] = array();
        foreach ($lookup[$row['id']] as $otherid) {
            array_push($json["otherRecordIDs"], array_merge($staticData, $otherid));
        }
        $db->query("update version_history set note = $3 where id = $1 and version = $2;", array($row["id"], $row["version"], json_encode($json, JSON_PRETTY_PRINT)));
    }
}