<?php

/**
 * EAC-CPF Parser File
 *
 * Contains the parser for EAC-CPF files into PHP Identity Constellation objects.
 *
 * License:
 *
 *
 * @author Robbie Hott
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @copyright 2015 the Rector and Visitors of the University of Virginia, and
 *            the Regents of the University of California
 */
namespace snac\util;

/**
 * EAC-CPF Parser
 *
 * This class provides the utility to parser EAC-CPF XML files into PHP Identity constellations.
 * After parsing, it returns the \snac\data\Constellation object and provides a method to
 * access any tags or attributes from the file (including their values) that were not
 * understood by the parser.
 *
 * @author Robbie Hott
 *        
 */
class EACCPFParser {



    /**
     *
     * @var string[] ARK ID used for warnings and error messages. Set after parsing begins. See setArkID();
     */
    private $arkID = "";


    /**
     *
     * @var string[] The list of namespaces in the document
     */
    private $namespaces;

    /**
     *
     * @var string[] The list of unknown elements and their values
     */
    private $unknowns;

    /**
     * Parse a file into an identity constellation.
     *
     * @param string $filename Filename of the file to parse
     * @return \snac\data\Constellation The resulting constellation
     */
    public function parseFile($filename) {

        try {
            return $this->parse(file_get_contents($filename));
        } catch (\Exception $e) {
            throw new \snac\exceptions\SNACParserException($e->getMessage());
        }
    }

    public function getTerm($termString, $vocab) {
        $term = new \snac\data\Term();
        $term->setTerm($termString);
        return $term;
    }

    /**
     * Parse a string containing EAC-CPF XML into an identity constellation.
     *
     * @param string $xmlText XML text to parse
     * @return \snac\data\Constellation The resulting constellation
     */
    public function parse($xmlText) {

        $xml = simplexml_load_string($xmlText);
        
        $identity = new \snac\data\Constellation();
      
        $this->unknowns = array ();
        $this->namespaces = $xml->getNamespaces(true);

        $languageDeclaration = new \snac\data\Language();
        
        foreach ($this->getChildren($xml) as $node) {
            if ($node->getName() == "control") {
                
                foreach ($this->getChildren($node) as $control) {
                    $catts = $this->getAttributes($control);
                    switch ($control->getName()) {
                    case "recordId":
                        $identity->setArkID((string) $control);
                        $this->arkID = $identity->toArray()['ark']; // Yes, toArray() and an index key all on one line.
                        $this->markUnknownAtt(
                            array (
                                $node->getName(),
                                $control->getName()
                            ), $catts);
                        break;
                    case "otherRecordId":
                        $term = $this->getTerm($this->getValue($catts["localType"]),"otherRecordType");
                        $sameas = new \snac\data\SameAs();
                        $sameas->setType($term);
                        $sameas->setURI((string) $control);
                        $identity->addOtherRecordID($sameas);
                        break;
                    case "maintenanceStatus":
                        $identity->setMaintenanceStatus($this->getTerm((string) $control, "maintenanceStatus"));
                        $this->markUnknownAtt(
                            array (
                                $node->getName(),
                                $control->getName()
                            ), $catts);
                        break;
                    case "maintenanceAgency":
                        $agencyInfo = $this->getChildren($control);
                        for($i = 1; $i < count($agencyInfo); $i++)
                            $this->markUnknownTag(
                                array (
                                    $node->getName,
                                    $control->getName()
                                ), 
                                array (
                                    $agencyInfo[$i]
                                ));
                        $identity->setMaintenanceAgency(trim((string) $agencyInfo[0]));
                        $this->markUnknownAtt(
                            array (
                                $node->getName(),
                                $control->getName(),
                                $agencyInfo[0]->getName()
                            ), $this->getAttributes($agencyInfo[0]));
                        $this->markUnknownAtt(
                            array (
                                $node->getName(),
                                $control->getName()
                            ), $catts);
                        break;
                    case "languageDeclaration":
                        foreach ($this->getChildren($control) as $lang) {
                            $latts = $this->getAttributes($lang);
                            switch ($lang->getName()) {
                            case "language":
                                if (isset($latts["languageCode"])) {
                                    $code = $latts["languageCode"];
                                    unset($latts["languageCode"]);
                                }
                                // Set the language term globally
                                $languageTerm = $languageDeclaration->getLanguage();
                                if ($languageTerm == null)
                                    $languageTerm = new \snac\data\Term();
                                $languageTerm->setTerm((string) $lang);
                                $languageTerm->setURI($code);
                                $languageDeclaration->setLanguage($languageTerm);
                                $this->markUnknownAtt(
                                    array (
                                        $node->getName(),
                                        $control->getName(),
                                        $lang->getName()
                                    ), $latts);
                                break;
                            case "script":
                                if (isset($latts["scriptCode"])) {
                                    $code = $latts["scriptCode"];
                                    unset($latts["scriptCode"]);
                                }
                                // Set the script term globally
                                $scriptTerm = $languageDeclaration->getScript();
                                if ($scriptTerm == null)
                                    $scriptTerm = new \snac\data\Term();
                                $scriptTerm->setTerm((string) $lang);
                                $scriptTerm->setURI($code);
                                $languageDeclaration->setScript($scriptTerm);
                                $this->markUnknownAtt(
                                    array (
                                        $node->getName(),
                                        $control->getName(),
                                        $lang->getName()
                                    ), $latts);
                                break;
                            default:
                                $this->markUnknownTag(
                                    array (
                                        $node->getName(),
                                        $control->getName()
                                    ), $lang);
                            }
                        }
                        $this->markUnknownAtt(
                            array (
                                $node->getName(),
                                $control->getName()
                            ), $catts);
                        break;
                    case "maintenanceHistory":
                        foreach ($this->getChildren($control) as $mevent) {
                            $event = new \snac\data\MaintenanceEvent();
                            foreach ($this->getChildren($mevent) as $mev) {
                                $eatts = $this->getAttributes($mev);
                                switch ($mev->getName()) {
                                case "eventType":
                                    $event->setEventType($this->getTerm((string) $mev, "eventType"));
                                    break;
                                case "eventDateTime":
                                    $event->setEventDateTime((string) $mev);
                                    if (isset($eatts["standardDateTime"])) {
                                        $event->setStandardDateTime($eatts["standardDateTime"]);
                                        unset($eatts["standardDateTime"]);
                                    }
                                    break;
                                case "agentType":
                                    $event->setAgentType($this->getTerm((string) $mev, "agentType"));
                                    break;
                                case "agent":
                                    $event->setAgent((string) $mev);
                                    break;
                                case "eventDescription":
                                    $event->setEventDescription((string) $mev);
                                    break;
                                default:
                                    $this->markUnknownTag(
                                        array (
                                            $node->getName(),
                                            $control->getName(),
                                            $mevent->getName()
                                        ), $mev);
                                }
                                $this->markUnknownAtt(
                                    array (
                                        $node->getName(),
                                        $control->getName(),
                                        $mevent->getName(),
                                        $mev->getName()
                                    ), $eatts);
                            }
                            $this->markUnknownAtt(
                                array (
                                    $node->getName(),
                                    $control->getName(),
                                    $mevent->getName()
                                ), $this->getAttributes($mevent));
                                
                            $identity->addMaintenanceEvent($event);
                        }
                        $this->markUnknownAtt(
                            array (
                                $node->getName(),
                                $control->getName()
                            ), $catts);
                        break;
                    case "conventionDeclaration":
                        $cd = new \snac\data\ConventionDeclaration();
                        $cd->setText($control->asXML());
                        $identity->addConventionDeclaration($cd);
                        $this->markUnknownAtt(
                            array (
                                $node->getName(),
                                $control->getName()
                            ), $catts);
                        break;
                    case "sources":
                        foreach ($this->getChildren($control) as $source) {
                            $satts = $this->getAttributes($source);
                            $sourceObj = new \snac\data\Source();
                            $sourceObj->setType($this->getTerm($this->getValue($satts['type']), "sourceType"));
                            $sourceObj->setURI($satts['href']);
                            foreach ($this->getChildren($source) as $innerSource) {
                                if ($innerSource->getName() == "objectXMLWrap")
                                    $sourceObj->setText($innerSource->asXML());
                                else if ($innerSource->getName() == "descriptiveNote")
                                    $sourceObj->setNote($innerSource->asXML());
                                else
                                    $this->markUnknownTag(
                                        array (
                                            $node->getName(),
                                            $control->getName(),
                                            $source->getName()
                                        ), $innerSource);
                            }
                            $identity->addSource($sourceObj);
                        }
                        break;
                    default:
                        $this->markUnknownTag(
                            array (
                                $node->getName()
                            ), 
                            array (
                                $control
                            ));
                    }
                }
            } elseif ($node->getName() == "cpfDescription") {
                
                foreach ($this->getChildren($node) as $desc) {
                    $datts = $this->getAttributes($desc);
                    
                    switch ($desc->getName()) {
                    case "identity":
                        foreach ($this->getChildren($desc) as $ident) {
                            $iatts = $this->getAttributes($ident);
                            switch ($ident->getName()) {
                            case "entityId":
                                $identity->addOtherRecordID("entityId", (string) $ident);
                                break;
                            case "entityType":
                                $identity->setEntityType($this->getTerm((string) $ident, "entityType"));
                                break;
                            case "nameEntry":
                                $nameEntry = new \snac\data\NameEntry();
                                if (isset($iatts["preferenceScore"])) {
                                    $nameEntry->setPreferenceScore($iatts["preferenceScore"]);
                                    unset($iatts["preferenceScore"]);
                                }
                                if (isset($iatts["lang"])) {
                                    $nameEntry->setLanguage($iatts["lang"]);
                                    unset($iatts["lang"]);
                                }
                                if (isset($iatts["scriptCode"])) {
                                    $nameEntry->setScriptCode($iatts["scriptCode"]);
                                    unset($iatts["scriptCode"]);
                                }
                                foreach ($this->getChildren($ident) as $npart) {
                                    switch ($npart->getName()) {
                                    case "part":
                                        $nameEntry->setOriginal((string) $npart);
                                        break;
                                    case "alternativeForm":
                                    case "authorizedForm":
                                        $nameEntry->addContributor($npart->getName(), (string) $npart);
                                        break;
                                    case "useDates":
                                        foreach ($this->getChildren($npart) as $dateEntry) {
                                            if ($dateEntry->getName() == "dateRange" ||
                                                $dateEntry->getName() == "date") {
                                                $nameEntry->setUseDates(
                                                    $this->parseDate($dateEntry, 
                                                                     array (
                                                                         $node->getName(),
                                                                         $desc->getName(),
                                                                         $ident->getName(),
                                                                         $npart->getName()
                                                                     )));
                                            } else {
                                                $this->markUnknownTag(
                                                    array (
                                                        $node->getName(),
                                                        $desc->getName(),
                                                        $ident->getName(),
                                                        $npart->getName()
                                                    ), array (
                                                        $dateEntry
                                                    ));
                                            }
                                        }
                                        break;
                                    default:
                                        $this->markUnknownTag(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $ident->getName()
                                            ), 
                                            array (
                                                $npart
                                            ));
                                    }
                                    $this->markUnknownAtt(
                                        array (
                                            $node->getName(),
                                            $desc->getName(),
                                            $ident->getName(),
                                            $npart->getName()
                                        ), $this->getAttributes($npart));
                                }
                                $identity->addNameEntry($nameEntry);
                                break;
                            default:
                                $this->markUnknownTag(
                                    array (
                                        $node->getName(),
                                        $desc->getName()
                                    ), 
                                    array (
                                        $ident
                                    ));
                            }
                            $this->markUnknownAtt(
                                array (
                                    $node->getName(),
                                    $desc->getName(),
                                    $ident->getName()
                                ), $iatts);
                        }
                        break;
                    case "description":
                        foreach ($this->getChildren($desc) as $desc2) {
                            $d2atts = $this->getAttributes($desc2);
                            switch ($desc2->getName()) {
                            case "existDates":
                                foreach ($this->getChildren($desc2) as $dates) {
                                    switch ($dates->getName()) {
                                    case "dateSet":
                                        foreach ($this->getChildren($dates) as $dateEntry) {
                                            $dateEntryName = $dateEntry->getName();
                                            if ($dateEntryName == "dateRange" || $dateEntryName == "date") {
                                                $date = $this->parseDate($dateEntry, 
                                                                         array (
                                                                             $node->getName(),
                                                                             $desc->getName(),
                                                                             $desc2->getName(),
                                                                             $dates->getName()
                                                                         ));
                                                $identity->addExistDates($date);
                                            } else {
                                                $this->markUnknownTag(
                                                    array (
                                                        $node->getName(),
                                                        $desc->getName(),
                                                        $desc2->getName(),
                                                        $dates->getName()
                                                    ), 
                                                    array (
                                                        $dateEntry
                                                    ));
                                            }
                                        }
                                        break;
                                    case "dateRange":
                                    case "date":
                                        $date = $this->parseDate($dates, 
                                                                 array (
                                                                     $node->getName() . $desc->getName(),
                                                                     $desc2->getName()
                                                                 ));
                                        $identity->addExistDates($date);
                                        break;
                                    case "descriptiveNote":
                                        $identity->setExistDatesNote((string) $dates);
                                        $this->markUnknownAtt(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $desc2->getName(),
                                                $dates->getName()
                                            ), $this->getAttributes($dates));
                                        break;
                                    default:
                                        $this->markUnknownTag(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $desc2->getName()
                                            ), 
                                            array (
                                                $dates
                                            ));
                                    }
                                }
                                break;
                            case "place":
                                $place = new \snac\data\Place();
                                $platts = $this->getAttributes($desc2);
                                if (isset($platts["localType"])) {
                                    $place->setType($this->getTerm($this->getValue($platts["localType"]), "placeType"));
                                    unset($platts["localType"]);
                                }
                                foreach ($this->getChildren($desc2) as $placePart) {
                                    switch ($placePart->getName()) {
                                    case "date":
                                    case "dateRange":
                                        $place->setDateRange(
                                            $this->parseDate($placePart, 
                                                             array (
                                                                 $node->getName(),
                                                                 $desc->getName(),
                                                                 $desc2->getName()
                                                             )));
                                        break;
                                    case "descriptiveNote":
                                        $place->setNote((string) $placePart);
                                        $this->markUnknownAtt(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $desc2->getName(),
                                                $placePart->getName()
                                            ), $this->getAttributes($placePart));
                                        break;
                                    case "placeRole":
                                        $place->setRole($this->getTerm((string) $placePart, "placeRole"));
                                        $this->markUnknownAtt(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $desc2->getName(),
                                                $placePart->getName()
                                            ), $this->getAttributes($placePart));
                                        break;
                                    case "placeEntry":
                                        $place->addPlaceEntry(
                                            $this->parsePlaceEntry($placePart, 
                                                                   array (
                                                                       $node->getName(),
                                                                       $desc->getName(),
                                                                       $desc2->getName()
                                                                   )));
                                        break;
                                    default:
                                        $this->markUnknownTag(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $desc2->getName()
                                            ), 
                                            array (
                                                $placePart
                                            ));
                                    }
                                }
                                $identity->addPlace($place);
                                $this->markUnknownAtt(
                                    array (
                                        $node->getName(),
                                        $desc->getName(),
                                        $desc2->getName()
                                    ), $platts);
                                break;
                            case "localDescription":
                                $subTags = $this->getChildren($desc2);
                                $subTag = $subTags[0];
                                for($i = 1; $i < count($subTags); $i++) {
                                    $this->markUnknownTag(
                                        array (
                                            $node->getName(),
                                            $desc->getName(),
                                            $desc2->getName()
                                        ), 
                                        array (
                                            $subTags[$i]
                                        ));
                                }
                                switch ($d2atts["localType"]) {
                                    // Each of these is in a sub element
                                case "http://socialarchive.iath.virginia.edu/control/term#AssociatedSubject":
                                    $subject = new \snac\data\Subject();
                                    $subject->setTerm($this->getTerm((string) $subTag, "subject"));
                                    $identity->addSubject($subject);
                                    break;
                                case "http://viaf.org/viaf/terms#nationalityOfEntity":
                                    // TODO: Sometimes nationality has non-standard placeEntry with only country code
                                    $term = $this->getTerm((string) $subTag, "nationality");
                                    $nationality = new \snac\data\Nationality();
                                    $nationality->setTerm($term);
                                    $identity->addNationality($nationality);
                                    break;
                                case "http://viaf.org/viaf/terms#gender":
                                    $gender = new \snac\data\Gender();
                                    $gender->setTerm($this->getTerm($this->getValue((string) $subTag), "gender"));
                                    $identity->addGender($gender);
                                    break;
                                default:
                                    $this->markUnknownTag(
                                        array (
                                            $node->getName(),
                                            $desc->getName()
                                        ), 
                                        array (
                                            $desc2
                                        ));
                                }
                                break;
                            case "languageUsed":
                                $language = new \snac\data\Language();
                                $updatedLanguage = false;
                                foreach ($this->getChildren($desc2) as $lang) {
                                    $latts = $this->getAttributes($lang);
                                    switch ($lang->getName()) {
                                    case "language":
                                        if (isset($latts["languageCode"])) {
                                            $code = $latts["languageCode"];
                                            unset($latts["languageCode"]);
                                        }
                                        $tmp = new \snac\data\Term();
                                        $tmp->setTerm((string) $lang);
                                        $tmp->setURI($code);
                                        $language->setLanguage($tmp);
                                        $updatedLanguage = true;
                                        $this->markUnknownAtt(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $desc2->getName(),
                                                $lang->getName()
                                            ), $latts);
                                        break;
                                    case "script":
                                        if (isset($latts["scriptCode"])) {
                                            $code = $latts["scriptCode"];
                                            unset($latts["scriptCode"]);
                                        }
                                        $tmp = new \snac\data\Term();
                                        $tmp->setTerm((string) $lang);
                                        $tmp->setURI($code);
                                        $language->setScript($tmp);
                                        $updatedLanguage = true;
                                        $this->markUnknownAtt(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $desc2->getName(),
                                                $lang->getName()
                                            ), $latts);
                                        break;
                                    default:
                                        $this->markUnknownTag(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $desc2->getName()
                                            ), $lang);
                                    }
                                }
                                if ($updatedLanguage)
                                    $identity->addLanguageUsed($language);
                                $this->markUnknownAtt(
                                    array (
                                        $node->getName(),
                                        $desc->getName(),
                                        $desc2->getName()
                                    ), $d2atts);
                                break;
                            case "generalContext":
                                $gc = new \snac\data\GeneralContext();
                                $gc->setText($desc2->asXML());
                                $identity->addGeneralContext($gc);
                                break;
                            case "legalStatus":
                                $legalStatusTerm = new \snac\data\Term();
                                foreach ($this->getChildren($desc2) as $legal) {
                                    $legalAtts = $this->getAttributes($legal);
                                    switch ($legal->getName()) {
                                    case "term":
                                        $legalStatusTerm->setTerm((string) $legal);
                                        if (isset($legalAtts["vocabularySource"])) {
                                            $legalStatusTerm->setURI($legalAtts["vocabularySource"]);
                                            unset($legalAtts["vocabularySource"]);
                                        }
                                        break;
                                    default:
                                        $this->markUnknownTag(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $desc2->getName()
                                            ), 
                                            array (
                                                $legal
                                            ));
                                    }
                                    $this->markUnknownAtt(
                                        array (
                                            $node->getName(),
                                            $desc->getName(),
                                            $desc2->getName(),
                                            $legal->getName()
                                        ), $legalAtts);
                                }
                                if (!$legalStatusTerm->isEmpty()) {
                                    $legalStatus = new \snac\data\LegalStatus();
                                    $legalStatus->setTerm($legalStatusTerm);
                                    $identity->addLegalStatus($legalStatus);
                                }
                                break;
                            case "mandate":
                                $mandate = new \snac\data\Mandate();
                                $mandate->setText($desc2->asXML());
                                $identity->addMandate($mandate);
                                break;
                            case "structureOrGenealogy":
                                $sog = new \snac\data\StructureOrGenealogy();
                                $sog->setText($desc2->asXML());
                                $identity->addStructureOrGenealogy($sog);
                                break;
                            case "occupation":
                                $occupation = new \snac\data\Occupation();
                                foreach ($this->getChildren($desc2) as $occ) {
                                    $oatts = $this->getAttributes($occ);
                                    switch ($occ->getName()) {
                                    case "term":
                                        $occupation->setTerm($this->getTerm((string) $occ, "occupation"));
                                        if (isset($oatts["vocabularySource"])) {
                                            $occupation->setVocabularySource($oatts["vocabularySource"]);
                                            unset($oatts["vocabularySource"]);
                                        }
                                        break;
                                    case "descriptiveNote":
                                        $occupation->setNote((string) $occ);
                                        break;
                                    case "dateRange":
                                        $date = $this->parseDate($occ, 
                                                                 array (
                                                                     $node->getName(),
                                                                     $desc->getName(),
                                                                     $desc2->getName()
                                                                 ));
                                        $occupation->setDateRange($date);
                                        break;
                                    default:
                                        $this->markUnknownTag(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $desc2->getName()
                                            ), 
                                            array (
                                                $occ
                                            ));
                                    }
                                    $this->markUnknownAtt(
                                        array (
                                            $node->getName(),
                                            $desc->getName(),
                                            $desc2->getName(),
                                            $occ->getName()
                                        ), $oatts);
                                }
                                $identity->addOccupation($occupation);
                                $this->markUnknownAtt(
                                    array (
                                        $node->getName(),
                                        $desc->getName(),
                                        $desc2->getName()
                                    ), $this->getAttributes($desc2));
                                break;
                            case "function":
                                $function = new \snac\data\SNACFunction();
                                foreach ($this->getChildren($desc2) as $fun) {
                                    $fatts = $this->getAttributes($fun);
                                    switch ($fun->getName()) {
                                    case "term":
                                        $function->setTerm($this->getTerm((string) $fun, "function"));
                                        if (isset($fatts["vocabularySource"])) {
                                            $function->setVocabularySource($fatts["vocabularySource"]);
                                            unset($fatts["vocabularySource"]);
                                        }
                                        break;
                                    case "descriptiveNote":
                                        $function->setNote((string) $fun);
                                        break;
                                    case "dateRange":
                                        $date = $this->parseDate($fun, 
                                                                 array (
                                                                     $node->getName(),
                                                                     $desc->getName(),
                                                                     $desc2->getName()
                                                                 ));
                                        $function->setDateRange($date);
                                        break;
                                    default:
                                        $this->markUnknownTag(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $desc2->getName()
                                            ), 
                                            array (
                                                $fun
                                            ));
                                        $this->markUnknownAtt(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $desc2->getName(),
                                                $fun->getName()
                                            ), $fatts);
                                    }
                                }
                                $fatts = $this->getAttributes($desc2);
                                if (isset($fatts["localType"])) {
                                    $function->setType($fatts["localType"]);
                                    unset($fatts["localType"]);
                                }
                                $identity->addFunction($function);
                                $this->markUnknownAtt(
                                    array (
                                        $node->getName(),
                                        $desc->getName(),
                                        $desc2->getName()
                                    ), $fatts);
                                break;
                            case "biogHist":
                                $bh = new \snac\data\BiogHist();
                                $bh->setText($desc2->asXML());
                                $bh->setLanguage($languageDeclaration);
                                $identity->addBiogHist($bh);
                                break;
                            default:
                                $this->markUnknownTag(
                                    array (
                                        $node->getName(),
                                        $desc->getName()
                                    ), 
                                    array (
                                        $desc2
                                    ));
                            }
                        }
                        break;
                    case "relations":
                        foreach ($this->getChildren($desc) as $rel) {
                            $ratts = $this->getAttributes($rel);
                            // We want 'href' to always exist. If it doesn't, warn, and set it to the empty string.
                            if ( ! isset($ratts['href']))
                            {
                                // In retrospect, we can silently just make this an empty string, probably.
                                /* 
                                 * $message = sprintf("Warning: empty href in relations for: %s\n", $this->arkID);
                                 * $stderr = fopen('php://stderr', 'w');
                                 * fwrite($stderr,"  $message\n");
                                 * fclose($stderr); 
                                 */
                                $ratts['href'] = "";
                            }
                            switch ($rel->getName()) {
                            case "cpfRelation":
                                $relation = new \snac\data\ConstellationRelation();
                                $relation->setType($this->getTerm($this->getValue($ratts["arcrole"]), "relationType"));
                                $relation->setTargetArkID($ratts['href']);
                                $relation->setTargetType($this->getTerm($this->getValue($ratts['role']), "entityType"));
                                $relation->setAltType($this->getTerm($this->getValue($ratts["type"]), "altType"));
                                if (isset($ratts['cpfRelationType'])) {
                                    $relation->setCPFRelationType($this->getTerm($ratts['cpfRelationType'], "cpfRelationType"));
                                    unset($ratts["cpfRelationType"]);
                                }
                                unset($ratts["arcrole"]);
                                unset($ratts["href"]);
                                unset($ratts["role"]);
                                unset($ratts["type"]);
                                $children = $this->getChildren($rel);
                                if (! empty($children)) {
                                    $relation->setContent((string) $children[0]);
                                }
                                foreach ($children as $child) {
                                    switch ($child->getName()) {
                                    case "relationEntry":
                                        $relation->setContent((string) $child);
                                        break;
                                    case "date":
                                    case "dateRange":
                                        $relation->setDates(
                                            $this->parseDate($child, 
                                                             array (
                                                                 $node->getName(),
                                                                 $desc->getName(),
                                                                 $rel->getName()
                                                             )));
                                        break;
                                    case "descriptiveNote":
                                        $relation->setNote($child->asXML());
                                        $this->markUnknownAtt(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $rel->getName(),
                                                $child->getName()
                                            ), $this->getAttributes($child));
                                        break;
                                    default:
                                        $this->markUnknownTag(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $rel->getName()
                                            ), 
                                            array (
                                                $child
                                            ));
                                    }
                                }
                                $this->markUnknownAtt(
                                    array (
                                        $node->getName(),
                                        $desc->getName(),
                                        $rel->getName()
                                    ), $ratts);
                                $identity->addRelation($relation);
                                break;
                            case "resourceRelation":
                                $relation = new \snac\data\ResourceRelation();
                                $relation->setDocumentType($this->getTerm($this->getValue($ratts["role"]), "documentType"));
                                $relation->setLink($ratts['href']);
                                $relation->setLinkType($this->getTerm($this->getValue($ratts['type']), "linkType"));
                                $relation->setRole($this->getTerm($this->getValue($ratts['arcrole']), "arcRole"));
                                foreach ($this->getChildren($rel) as $relItem) {
                                    switch ($relItem->getName()) {
                                    case "relationEntry":
                                        $relation->setContent((string) $relItem);
                                        $relAtts = $this->getAttributes($relItem);
                                        if (isset($relAtts["localType"])) {
                                            $relation->setRelationEntryType($this->getTerm($this->getValue($relAtts["localType"]), "relationType"));
                                            unset($relAtts["localType"]);
                                        }
                                        $this->markUnknownAtt(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $rel->getName(),
                                                $relItem->getName()
                                            ), $relAtts);
                                        break;
                                    case "objectXMLWrap":
                                        $relation->setSource($relItem->asXML());
                                        $this->markUnknownAtt(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $rel->getName(),
                                                $relItem->getName()
                                            ), $this->getAttributes($relItem));
                                        break;
                                    case "descriptiveNote":
                                        $relation->setNote($relItem->asXML());
                                        $this->markUnknownAtt(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $rel->getName(),
                                                $relItem->getName()
                                            ), $this->getAttributes($relItem));
                                        break;
                                    default:
                                        $this->markUnknownTag(
                                            array (
                                                $node->getName(),
                                                $desc->getName(),
                                                $rel->getName()
                                            ), 
                                            array (
                                                $relItem
                                            ));
                                    }
                                }
                                $identity->addResourceRelation($relation);
                                break;
                            default:
                                $this->markUnknownTag(
                                    array (
                                        $node->getName(),
                                        $desc->getName()
                                    ), 
                                    array (
                                        $rel
                                    ));
                            }
                        }
                        break;
                    default:
                        $this->markUnknownTag(
                            array (
                                $node->getName()
                            ), 
                            array (
                                $desc
                            ));
                    }
                }
            } else {
                $this->markUnknownTag(array (), array (
                    $node->getName()
                ));
            }
        }
        return $identity;
    }

    /**
     * Get the tags and attributes that were not understood by this parser.
     * The resulting strings are
     * <code>
     * full/path/to/tag :: value
     * full/path/to/@att :: value
     * </code>
     *
     * @return string[] List of tags and attributes and their values
     */
    public function getMissing() {

        return $this->unknowns;
    }

    /**
     * Get the value of the parameter string, stripping off any namespace
     * portions and returning only the text value.
     *
     * Currently, this splits the string based on the pound sign (#) and
     * returns the end of the string.
     *
     * @param string $value Tag/Attribute value to strip the controlled vocab from
     * @return string Cleaned string, with no namespace text
     */
    private function getValue($value) {
        $parts = explode("#", $value);
        if (count($parts) == 2)
            return $parts[1];
        else
            return $value;
    }

    /**
     * Get the attributes for a given SimpleXMLElement, ignoring all namespaces.
     * This is a way
     * to get around the need to query for each namespace separately
     *
     * @param SimpleXMLElement $element Element to query for attributes
     * @return string[] Attributes and values, attName => value
     */
    private function getAttributes($element) {

        $att = array ();
        
        foreach ($element->attributes() as $k => $v)
            $att[$k] = (string) $v;
        
        foreach ($this->namespaces as $s => $n) {
            foreach ($element->attributes($n) as $k => $v)
                $att[$k] = (string) $v;
        }
        return $att;
    }

    /**
     * Get the children for a given SimpleXMLElement, ignoring all namespaces.
     * This is a way to
     * get around the need to query for each namespace separately.
     *
     * @param SimpleXMLElement $element Element to query for children
     * @return SimpleXMLElement[] array of children elements from any namespace
     */
    private function getChildren($element) {

        $children = array ();
        
        foreach ($element->children() as $k => $v) {
            // if (!isset($children[$k])) $children[$k] = array();
            // array_push($children, $v);
        }
        
        foreach ($this->namespaces as $s => $n) {
            foreach ($element->children($n) as $k => $v)
                // if (!isset($children[$k])) $children[$k] = array();
                array_push($children, $v);
        }
        return $children;
    }

    /**
     * Mark a tag or element as unknown to this parser.
     *
     * Adds the given information to the list of missing data.
     *
     * @param string[] $xpath Ordered array of the path names down to the current element.
     * @param string[] $missing Array of missing elements (tag or att) as "name"=>"value" pairs.
     * @param boolean $isTag Flag to determine if the $missing is a list of tags or attributes.
     */
    private function markUnknowns($xpath, $missing, $isTag) {

        $path = implode("/", $xpath);
        $path .= "/";
        
        if (! $isTag) {
            $path .= "@";
        }
        
        foreach ($missing as $k => $v) {
            array_push($this->unknowns, $path . $k . " :: " . $v);
        }
    }

    /**
     * Mark an unknown attribute from the given path and element
     *
     * @param string[] $xpath Ordered array of the path names down to just before the current element.
     * @param string[] $missing Array of missing tags as "name"=>"value" pairs.
     */
    private function markUnknownAtt($xpath, $missing) {

        $this->markUnknowns($xpath, $missing, false);
    }

    /**
     * Mark an unknown tag from the given path and element
     *
     * @param string[] $xpath Ordered array of the path names down to the current tag.
     * @param string[] $missing Array of missing attributes as "name"=>"value" pairs.
     */
    private function markUnknownTag($xpath, $missing) {

        foreach ($missing as $m) {
            // Mark this tag as missing
            $this->markUnknowns($xpath, array (
                $m->getName() => (string) $m
            ), true);
            // Mark all attributes of this tag as missing
            $this->markUnknowns(array_merge($xpath, array (
                $m->getName()
            )), $this->getAttributes($m), false);
            
            // Traverse down the children
            $this->markUnknownTag(array_merge($xpath, array (
                $m->getName()
            )), $this->getChildren($m));
        }
    }

    /**
     * Parse the given date element into its appropriate object.
     * This requires testing for both a dateRange, as well
     * as notBefore and notAfter ranges on each date given.
     *
     * @param \SimpleXMLElement $dateElement Date element to parse
     * @param string[] $xpath All pieces of the xpath leading up to the date to parse
     * @return \snac\data\SNACDate The resulting date object
     */
    private function parseDate($dateElement, $xpath) {

        $date = new \snac\data\SNACDate();
        if ($dateElement->getName() == "dateRange") {
            // Handle the date range
            $date->setRange(true);
            foreach ($this->getChildren($dateElement) as $dateTag) {
                $dateAtts = $this->getAttributes($dateTag);
                switch ($dateTag->getName()) {
                case "fromDate":
                    if (((string) $dateTag) != null && ((string) $dateTag) != '') {
                        $date->setFromDate((string) $dateTag, $dateAtts["standardDate"], $this->getTerm($this->getValue($dateAtts["localType"]), "dateType"));
                        $notBefore = null;
                        $notAfter = null;
                        if (isset($dateAtts["notBefore"]))
                            $notBefore = $dateAtts["notBefore"];
                        if (isset($dateAtts["notAfter"]))
                            $notAfter = $dateAtts["notAfter"];
                        $date->setFromDateRange($notBefore, $notAfter);
                            
                        unset($dateAtts["notBefore"]);
                        unset($dateAtts["notAfter"]);
                        unset($dateAtts["standardDate"]);
                        unset($dateAtts["localType"]);
                        $this->markUnknownAtt(
                            array_merge($xpath, 
                                        array (
                                            $dateElement->getName(),
                                            $dateTag->getName()
                                        )), $dateAtts);
                    }
                    break;
                case "toDate":
                    if (((string) $dateTag) != null && ((string) $dateTag) != '') {
                        $date->setToDate((string) $dateTag, $dateAtts["standardDate"], $this->getTerm($this->getValue($dateAtts["localType"]), "dateType"));
                        $notBefore = null;
                        $notAfter = null;
                        if (isset($dateAtts["notBefore"]))
                            $notBefore = $dateAtts["notBefore"];
                        if (isset($dateAtts["notAfter"]))
                            $notAfter = $dateAtts["notAfter"];
                        $date->setToDateRange($notBefore, $notAfter);
                            
                        unset($dateAtts["notBefore"]);
                        unset($dateAtts["notAfter"]);
                        unset($dateAtts["standardDate"]);
                        unset($dateAtts["localType"]);
                        $this->markUnknownAtt(
                            array_merge($xpath, 
                                        array (
                                            $dateElement->getName(),
                                            $dateTag->getName()
                                        )), $dateAtts);
                    }
                    break;
                default:
                    $this->markUnknownTag(
                        array_merge($xpath, 
                                    array (
                                        $dateElement->getName()
                                    )), 
                        array (
                            $dateTag
                        ));
                }
            }
        } elseif ($dateElement->getName() == "date") {
            /* 
             * Sanity check standardDate. Unclear what should happen if we don't have a standardDate
             * value. This code leaves $date unchanged, and hopes that the initialization was sane.
             */
            if (isset($dateAtts["standardDate"]))
            {
                
                // Handle the single date that appears
                $date->setRange(false);
                $dateAtts = $this->getAttributes($dateElement);
                $date->setDate((string) $dateElement, $dateAtts["standardDate"], $this->getTerm($this->getValue($dateAtts["localType"]), "dateType"));
                $notBefore = null;
                $notAfter = null;
                if (isset($dateAtts["notBefore"]))
                    $notBefore = $dateAtts["notBefore"];
                if (isset($dateAtts["notAfter"]))
                    $notAfter = $dateAtts["notAfter"];
                $date->setDateRange($notBefore, $notAfter);
                
                unset($dateAtts["notBefore"]);
                unset($dateAtts["notAfter"]);
                unset($dateAtts["standardDate"]);
                unset($dateAtts["localType"]);
                $this->markUnknownAtt(
                    array_merge($xpath, 
                                array (
                                    $dateElement->getName()
                                )), $dateAtts);
            }
            else
            {
                /* Silently make dates with no standard date only partial complete.
                 * 
                 * $message = sprintf("Warning: empty standardDate in date for: %s\n", $this->arkID);
                 * $stderr = fopen('php://stderr', 'w');
                 * fwrite($stderr,"  $message\n");
                 * fclose($stderr); 
                 */
                $date->setDate((string) $dateElement, '', '');
            }
        }
        return $date;
    }

    /**
     * Parse a place entry XML tag into a \snac\data\PlaceEntry object.
     * This is a recursively called function, since
     * some placeEntry tags are nested. The best example is the snac:placeEntry tag, which may include the following
     * placeEntry tag variants:
     *
     * <ul>
     * <li>placeEntry</li>
     * <li>placeEntryMaybeSame</li>
     * <li>placeEntryLikelySame</li>
     * <li>placeEntryBestMaybeSame</li>
     * </ul>
     *
     * @param \SimpleXMLElement $placeTag Place element to parse
     * @param string[] $xpath all pieces of the xpath leading up to the $placeTag element
     * @return \snac\data\PlaceEntry resulting object
     */
    private function parsePlaceEntry($placeTag, $xpath) {

        $plAtts = $this->getAttributes($placeTag);
        $placeEntry = new \snac\data\PlaceEntry();
        
        if (isset($plAtts["latitude"])) {
            $placeEntry->setLatitude($plAtts["latitude"]);
            unset($plAtts["latitude"]);
        }
        if (isset($plAtts["longitude"])) {
            $placeEntry->setLongitude($plAtts["longitude"]);
            unset($plAtts["longitude"]);
        }
        if (isset($plAtts["localType"])) {
            $placeEntry->setType($this->getTerm($plAtts["localType"], "placeType"));
            unset($plAtts["localType"]);
        }
        if (isset($plAtts["administrationCode"])) {
            $placeEntry->setAdministrationCode($plAtts["administrationCode"]);
            unset($plAtts["administrationCode"]);
        }
        if (isset($plAtts["countryCode"])) {
            $placeEntry->setCountryCode($plAtts["countryCode"]);
            unset($plAtts["countryCode"]);
        }
        if (isset($plAtts["vocabularySource"])) {
            $placeEntry->setVocabularySource($plAtts["vocabularySource"]);
            unset($plAtts["vocabularySource"]);
        }
        if (isset($plAtts["certaintyScore"])) {
            $placeEntry->setCertaintyScore($plAtts["certaintyScore"]);
            unset($plAtts["certaintyScore"]);
        }
        $this->markUnknownAtt(array_merge($xpath, array (
                $placeTag->getName()
        )), $plAtts);
        
        // Set the original string. If this is a snac:placeEntry, it will be empty, but there will be a sub-placeEntry
        // that will be found below to overwrite the original string
        $placeEntry->setOriginal((string) $placeTag);
        
        foreach ($this->getChildren($placeTag) as $child) {
            switch ($child->getName()) {
                case "placeEntryBestMaybeSame":
                case "placeEntryLikelySame":
                    $placeEntry->setBestMatch(
                            $this->parsePlaceEntry($child, 
                                    array_merge($xpath, 
                                            array (
                                                    $placeTag->getName()
                                            ))));
                    break;
                case "placeEntryMaybeSame":
                    $placeEntry->addMaybeSame(
                            $this->parsePlaceEntry($child, 
                                    array_merge($xpath, 
                                            array (
                                                    $placeTag->getName()
                                            ))));
                    break;
                case "placeEntry":
                    $placeEntry->setOriginal((string) $child);
                    $this->markUnknownAtt(
                            array_merge($xpath, 
                                    array (
                                            $placeTag->getName(),
                                            $child->getName()
                                    )), $this->getAttributes($child));
                    break;
                default:
                    $this->markUnknownTag(
                            array_merge($xpath, 
                                    array (
                                            $placeTag->getName()
                                    )), array (
                                    $child
                            ));
            }
        }
        
        return $placeEntry;
    }
}
