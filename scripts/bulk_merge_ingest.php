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
global $limit;

$limit = 1000;

// $db = new \snac\server\database\DatabaseConnector();
$config = new snac\Config();

/* unbuffer stdin, stdout? Or something. */
stream_set_blocking(STDIN, 0);
stream_set_blocking(STDOUT, 0);

foo_main();
exit();


function foo_main () 
{
    global $argc, $argv, $config, $db, $limit;

    /* 
     * U for Util.
     * 
     * $dbu = new snac\server\database\DBUtil($db);
     * 
     * Don't create a DatabaseConnector here. DBUtil knows how to do that itself. Leave all the db stuff to
     * code that needs to manage it.
     */
    $dbu = new snac\server\database\DBUtil();

    list($appUserID, $role) = $dbu->getAppUserInfo('twl8n');
    printf("appUserID: %s role: %s\n", $appUserID, $role);
    
    // Works
    // printf("userid: %s\n", snac\Config::$userid);

    // Works also
    printf("userid: %s\n", $config::$userid);

    // If no file was parsed, then print the output that something went wrong
    if ($argc < 2)
    {
        echo "No files given\n\nSample usage: ./cpf_vocab_test directory\ncpf_vocab_test.php /data/merge 2> cvt.log\n\n";
        exit();
    }

    if (is_dir($argv[1]))
    {
        // Don't scan. Read each file from opendir() in the big while loop below.
        if (0==1)
        {
            printf("Scanning $argv[1]...\n");
            
            // PHP Fatal error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 4096 bytes) in
            // /lv1/home/twl8n/snac/cpf_vocab_test.php on line 59
            // $file_list = scandir($argv[1], SCANDIR_SORT_NONE);
            
            // var_export($file_list);
            // print interpolation outside "" doesn't work properly without the enclosing ().
            // print "Found " . (count($file_list) - 2) . " files\n";
            // printf() interprets extra args just fine without surrounding parens (unlike print()).
            
            printf("Found %s files\n", count($file_list) - 2);
        }
    }
    else
    {
        print "Not a directory: $argv[1]\n";
        exit();
    }
    
    load_vocab();

    // foreach ($file_list as $short_file)

    // Doesn't work, seems to be an issue with nested function calls.
    // while ($short_file = readdir(opendir($argv[1])))

    printf("Opening dir: $argv[1]\n");
    $dh = opendir($argv[1]);
    printf("Done.\n");

    $xx = 0;
    while ($short_file = readdir($dh))
    {
        if ($short_file == '.' or $short_file == '..')
        {
            continue;
        }
        $xx++;
        if ($xx > $limit)
        {
            exit();
        }

        // Create a full path file name
        $file = "$argv[1]/$short_file";

        // Create new parser for this file and parse it
        $eparser = new \snac\util\EACCPFParser();
        $constellationObj = $eparser->parseFile($file);

        
        $unparsedTags = $eparser->getMissing();
        if (empty($unparsedTags))
        {
            $vhInfo = $dbu->insertConstellation($constellationObj, $appUserID, $role, 'bulk ingest', 'bulk ingest of merged');
            check_vocabulary($constellationObj);
            // $msg = sprintf("File $file ok. vhInfo: %s", var_export($vhInfo, 1));
            $msg = sprintf("File $file ok.");
            quick_stderr($msg); // no terminal \n, the code will add that later
        }
        else
        {
            // For each unparsable tag and attribute in the parsed EAC-CPF, print it out

            // Print error messages to stderr, giving the user the option to io redirect to a separate log
            // file.
            foreach ($unparsedTags as $miss)
            {
                quick_stderr($miss);
                // fwrite($stderr,"  $miss\n");
            }
        }
    }
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

// This needs to be moved to SQL.php
function load_vocab()
{
    $db = new \snac\server\database\DatabaseConnector();

    $qq = 'check_vocab';
    $db->prepare($qq, 'select * from vocabulary');
    $res = $db->execute($qq, array());
    print "Execute done\n";
    
    $all_vocab = array();
    while ($row = $db->fetchrow($res))
    {
        $key = $row['type'] . '::' . $row['value'];
        // print "key: $key\n";
        // Save a $row reference in $all_vocab
        $all_vocab[$key] = $row;
    }
    printf("Vocabulary loaded. %s rows\n", count($all_vocab));
    // var_export($all_vocab);
    // Hmm. json_encode doesn't work on $all_vocab. Nothing prints. Curious.
    // print json_encode($all_vocab, JSON_PRETTY_PRINT) . "\n";
}

