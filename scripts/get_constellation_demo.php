#!/usr/bin/env php
<?php

  /* 
   * Database Connector Test File
   * 
   * Usage: bulk_merge_ingest.php /data/merge 2> bmi.log
   * 
   * License: @author Tom Laudeman @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
   * @copyright 2015 the Rector and Visitors of the University of Virginia, and the Regents of the University
   * of California
   * 
   * Include the global autoloader generated by composer
   * Only need this if this code *is* the server (aka standalone, not part of the server)
   *
   * Run a demo with function data:
   * scripts/get_constellation_demo.php 486 13129 | less
   *
   * That was determined by select * from function, and using the version and main_id from a record with a
   * non-null function_id and function_type.
   *
   * Run a demo with multiple otherId values:
   * scripts/get_constellation_demo.php 2 15 | less
   *
   * Get the example but running a query and looking for a main_id has multiple values for other_id. 
   * select * from otherid;
   *
   *
   * Run a demo with some subjects:
   *
   * scripts/get_constellation_demo.php 5 49
   *
   * Discovery version 5 and main_id 49 by looking for multi-subject main_id in the query results:
   * select * from subject limit 10;
   * 
   */
include "vendor/autoload.php";
include "src/snac/Config.php";

/* 
 * db connector test
 * @author Tom Laudeman
 * 
 * Tests the generic connection (part of the constructor) 
 */

global $argc, $argv;

// Is this being called so that other code like DBUtil won't need to call it? Seems like there's nothing in
// the config but the db connection info.
$config = new snac\Config();

/* unbuffer stdin, stdout? Or something. */
stream_set_blocking(STDIN, 0);
stream_set_blocking(STDOUT, 0);

foo_main();
exit();


function foo_main () 
{
    global $argc, $argv, $config;

    /* 
     * U for Util.
     * 
     *  Don't create a DatabaseConnector here. DBUtil knows how to do that itself. Leave all the db stuff to
     *  code that needs to manage it.
     */
    $dbu = new snac\server\database\DBUtil();

    list($appUserID, $role) = $dbu->getAppUserInfo('twl8n');
    printf("appUserID: %s role: %s\n", $appUserID, $role);
    
    // Works
    // printf("userid: %s\n", snac\Config::$userid);

    // Works also
    printf("userid: %s\n", $config::$userid);

    $xx = 0;
    if ($argc>1)
    {
        printf("Using version: %s main_id: %s\n", $argv[1], $argv[2]);
        $cObj = $dbu->selectConstellation(array('version' => $argv[1],
                                        'main_id' =>$argv[2]),
                                  $appUserID);
    }
    else
    {
        /*
         * demoConstellation() will return a single constellation that has a date (date_range), which is handy
         * for testing dates. Unfortunately, the returned constellation may not have a complete from-date and
         * to-date. You could add such a feature in SQL.php randomConstellationID().
         * 
         */
        $vhInfo = $dbu->demoConstellation();
        $cObj = $dbu->selectConstellation($vhInfo, $appUserID);
    }
    printf("todo: places, maintenanceEvents\n");        
    printf("Filled const: %s\n", $cObj->toJSON());
}

/* 
 * entityType (person)
 * otherRecordIDs->type (MergedRecord)
 * maintenanceStatus (revised)
 * maintenanceEvents->eventType (revised)
 * maintenanceEvents->agentType (machine)
 * sources->type (simple)
 * legalStatuses (?)
 * constellationLanguage (English)
 * constellationLanguageCode (eng)
 * constellationScript ()
 * constellationScriptCode (Zyyy)
 * language
 * languageCode
 * script
 * scriptCode
 * nameEntries[]->language
 * nameEntries[]->scriptCode
 * occupations[]->term
 * occupations[]->vocabularySource (what is this?)
 * existDates[]->fromType
 * existDates[]->toType
 * # why isn't this "cpfRelations" if the one below is "resourceRelations"?
 * relations[]->targetEntityType (Person, same as entityType?)
 * relations[]->type (associatedWith)
 * relations[]->altType (simple)
 * relations[]->cpfRelationType (from anf)
 * resourceRelations[]->documentType (ArchivalResource, is this value always the same?)
 * resourceRelations[]->linkType (simple, is this always the same?)
 * resourceRelations[]->entryType
 * resourceRelations[]->role (referencedIn)
 * relations->targetEntityType
 * relations->type
 * functions[]->?
 * places->type (AssociatedPlace, is this always the same?)
 * subjects[]
 * nationality
 * gender
 * generalContext
 * structureOrGenealogy
 * mandate
 */


function check_vocabulary($id)
{
    // Maybe later we can write the code to check this constellation to see if it has any vocabulary not
    // already in the db.

    if (0)
    {
        // Set to true to var_export() a single constellation $id object and exit.  Perhaps not too useful
        // since those are all private vars. Someone is going to have to write getters for all of them.

        $cfile = fopen('constellation_var.txt', 'w');
        fwrite($cfile, var_export($id, 1));
        fclose($cfile); 
        exit();
    }
    return 1;
}

// None too efficient since it opens and closes the stream constantly.
function quick_stderr ($message)
{
    $stderr = fopen('php://stderr', 'w');
    fwrite($stderr,"  $message\n");
    fclose($stderr); 
}
