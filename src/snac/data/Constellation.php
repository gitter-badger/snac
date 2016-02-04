<?php

/**
 * Identity Constellation File
 *
 * Contains the constellation information for an entire entity.
 *
 * License:
 *
 *
 * @author Robbie Hott
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @copyright 2015 the Rector and Visitors of the University of Virginia, and
 *            the Regents of the University of California
 */
namespace snac\data;

/**
 * Identity Constellation
 *
 * Stores all the information related to an identity constellation. Can be built in pieces, or imported
 * from an associative array.
 *
 * @author Robbie Hott
 *        
 */
class Constellation extends AbstractData {

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/control/recordId
     * 
     * @var string ARK identifier
     */
    private $ark = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/identity/entityType
     * 
     * @var \snac\data\Term Entity type
     */
    private $entityType = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/control/otherRecordId
     * * eac-cpf/cpfDescription/identity/entityID
     * 
     * @var \snac\data\SameAs[] Other record IDs by which this constellation may be known
     */
    private $otherRecordIDs = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/control/maintenanceStatus
     * 
     * @var string Current maintenance status
     */
    private $maintenanceStatus = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/control/maintenanceAgency/agencyName
     * 
     * @var string Latest maintenance agency
     */
    private $maintenanceAgency = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/control/maintenanceHistory/maintenanceEvent/*
     * 
     * @var \snac\data\MaintenanceEvent[] List of maintenance events performed on this constellation
     */
    private $maintenanceEvents = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * /eac-cpf/control/sources/source/@type
     * * /eac-cpf/control/sources/source/@href
     * 
     * @var \snac\data\Source[] List of sources
     */
    private $sources = null;
    
    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/description/legalStatus/term
     * * eac-cpf/cpfDescription/description/legalStatus/@vocabularySource
     * 
     *
     * @var \snac\data\LegalStatus[] List of legal statuses
     */
    private $legalStatuses = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/control/conventionDeclaration
     * 
     * @var \snac\data\ConventionDeclaration[] Convention declarations
     */
    private $conventionDeclarations = null;
    
    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/description/languageUsed/language
     * * eac-cpf/cpfDescription/description/languageUsed/language/@languageCode
     * * eac-cpf/cpfDescription/description/languageUsed/script
     * * eac-cpf/cpfDescription/description/languageUsed/script/@scriptCode
     * 
     * @var \snac\data\Language[] Languages used by the identity described
     */
    private $languagesUsed = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/identity/nameEntry
     * 
     * @var \snac\data\NameEntry[] List of name entries for this constellation
     */
    private $nameEntries = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/description/occupation/*
     * 
     * @var \snac\data\Occupation[] List of occupations
     */
    private $occupations = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/description/biogHist
     * 
     * @var \snac\data\BiogHist[] BiogHist entries for this constellation (in XML strings)
     */
    private $biogHists = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/relations/cpfRelation/*
     * 
     * @var \snac\data\ConstellationRelation[] Constellation relations
     */
    private $relations = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/relations/resourceRelation/*
     * 
     * @var \snac\data\ResourceRelation[] Resource relations
     */
    private $resourceRelations = null;

    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/description/function/*
     * 
     * @var \snac\data\SNACFunction[] Functions
     */
    private $functions = null;

    /**
     * A list of Place objects. 
     *
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/description/place/*
     * 
     * @var \snac\data\Place[] Places
     */
    private $places = null;
    
    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/description/localDescription/@localType=AssociatedSubject/term
     * 
     * @var \snac\data\Subject[] Subjects
     */
    private $subjects = null;
    
    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/description/localDescription/@localType=nationalityOfEntity/term
     * 
     * @var \snac\data\Nationality[] nationalities of this entity
     */
    private $nationalities = null;
    
    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/description/localDescription/@localType=gender/term
     * 
     * @var \snac\data\Gender[] Gender
     */
    private $genders = null;
    
    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/description/generalContext
     * 
     * @var \snac\data\GeneralContext[] General Contexts
     */
    private $generalContexts = null;
    
    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/description/structureOrGenealogy
     * 
     * @var \snac\data\StructureOrGenealogy[] Structure Or Genealogy information
     */
    private $structureOrGenealogies = null;
    
    /**
     * From EAC-CPF tag(s):
     * 
     * * eac-cpf/cpfDescription/description/mandate
     * 
     * @var \snac\data\Mandate[] Mandates
     */
    private $mandates = null;

    /**
     * Constructor for the class. See the abstract parent class for common methods setDBInfo() and getDBInfo().
     *
     * Any value for setMaxDateCount() >1 means any number of date objects.
     *
     * @param string[] $data A list of data suitable for fromArray(). This exists for use by internal code to
     * send objects around the system, not for generally creating a new object. Normal use is to call the
     * constructor without an argument, get an empty class and use the setters to fill in the properties.
     *
     * @return Constellation object
     * 
     */
    public function __construct($data = null) {
        $this->setMaxDateCount(2);
        if ($data == null) {
            $this->entityType = new \snac\data\Term();
            $this->otherRecordIDs = array ();
            $this->sources = array ();
            $this->maintenanceEvents = array ();
            $this->nameEntries = array ();
            $this->biogHists = array ();
            $this->occupations = array ();
            $this->relations = array ();
            $this->resourceRelations = array ();
            $this->functions = array ();
            $this->places = array ();
            $this->subjects = array();
            $this->legalStatuses = array();
            $this->genders = array();
            $this->nationalities = array();
            $this->languagesUsed = array();
            $this->conventionDeclarations = array();
            $this->generalContexts = array();
            $this->structureOrGenealogies = array();
            $this->mandates = array();
        } else
            parent::__construct($data);
    }

    /**
     * Get the ARK identifier URI for this constellation 
     * 
     * @return string ARK identifier
     *
     */
    public function getArk()
    {
        return $this->ark;
    }

    /**
     * Get the entity type of this constellation
     *
     * @return \snac\data\Term Entity type
     *
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * Get the other record IDs (SameAs) for this constellation 
     *
     * @return \snac\data\SameAs[] Other record IDs by which this constellation may be known
     *
     */
    public function getOtherRecordIDs()
    {
        return $this->otherRecordIDs;
    }

    /**
     * Get the maintenance Status for this constellation 
     * 
     * @return string Current maintenance status
     *
     */
    public function getMaintenanceStatus()
    {
        return $this->maintenanceStatus;
    }

    /**
     * Get the maintenance agency for this constellation
     *
     * @return string Latest maintenance agency
     *
     */
    public function getMaintenanceAgency()
    {
        return $this->maintenanceAgency;
    }

    /**
     * Get the list of maintenance events for this constellation 
     *
     * @return \snac\data\MaintenanceEvent[] List of maintenance events performed on this constellation
     *
     */
    public function getMaintenanceEvents()
    {
        return $this->maintenanceEvents;
    }

    /**
     * Get the list of sources for this constellation 
     * 
     * @return \snac\data\Source[] List of sources
     *
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Get the list of legal statuses for this constellation 
     * 
     * @return \snac\data\LegalStatus[] List of legal statuses
     *
     */
    public function getLegalStatuses()
    {
        return $this->legalStatuses;
    }

    /**
     * Get the convention declarations for this constellation
     *
     * @return \snac\data\ConventionDeclaration[] Convention declarations
     *
     */
    public function getConventionDeclarations()
    {
        return $this->conventionDeclarations;
    }

    /**
     * Get the Languages Used
     *
     * @return \snac\data\Language[] Languages and scripts used by the identity described
     *
     */
    public function getLanguagesUsed()
    {
        return $this->languagesUsed;
    }

    /**
     * Alias function for getLanguagesUsed(). Get the Languages Used. Called in DBUtil.
     *
     * @return \snac\data\Language[] Languages and scripts used by the identity described
     *
     */
    public function getLanguage()
    {
        return $this->getLanguagesUsed();
    }



    /**
     * Get the name entries for this constellation
     *
     * @return \snac\data\NameEntry[] List of name entries for this constellation
     *
     */
    public function getNameEntries()
    {
        return $this->nameEntries;
    }

    /**
     * Get the occupations for this constellation
     * 
     * @return \snac\data\Occupation[] List of occupations
     *
     */
    public function getOccupations()
    {
        return $this->occupations;
    }

    /**
     * Get the list of BiogHist for this constellation. Each BiogHist is presumed to be a translation in a
     * specific language. See getBiogHist() which returns only a single BiogHist.
     *
     * @return \snac\data\BiogHist[] An array of BiogHist ordered by language 3 letter code, or an empty list
     * if no BiogHist exists for this Constellation
     */
    public function getBiogHistList()
    {
        return $this->biogHists;
    }


    /**
     * Get the BiogHist for this constellation.
     *
     * This will by default get the first BiogHist for the entity.  If another
     * language is desired, it may be passed as a parameter.  In that case,
     * the biogHist will be given for that language.  If no biogHist exists
     * for that language, the first will be returned.
     *
     * @param \snac\data\Language $language optional Language of the desired BiogHist 
     *
     * @return \snac\data\BiogHist The desired BiogHist for this language, the first
     * BiogHist, or null if no BiogHist exists for this Constellation
     */
    public function getBiogHist($language = null)
    {
        if (count($this->biogHists) > 0) {
            if ($language == null) {
                // No language, so return the first
                return $this->biogHists[0];
            } else {
                // We have a language.  Start from the end, return matching language or first
                // entry
                $i = count($this->biogHists) - 1;
                for (; $i >= 0; $i--) {
                    // If languages match, then break and return this biogHist.
                    if ($this->biogHists[$i]->getLanguage()->getLanguage()->getID() == 
                        $language->getLanguage()->getID())
                        break;
                }
                // Will return either the appropriate biogHist or the biogHist[0]
                return $this->biogHists[$i];
            }
        }
        return null;
    }

    /**
     * Get the constellation relations for this constellation
     *
     * @return \snac\data\ConstellationRelation[] Constellation relations
     *
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * Get the resource relations for this constellation
     *
     * @var \snac\data\ResourceRelation[] Resource relations
     */
    public function getResourceRelations()
    {
        return $this->resourceRelations;
    }

    /**
     * Get the Functions for this constellation 
     *
     * @return \snac\data\SNACFunction[] Functions
     *
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * Get the places associated with this constellation
     *
     * @return \snac\data\Place[] Places
     *
     */
    public function getPlaces()
    {
        return $this->places;
    }

    /**
     * Get the subjects associated with this constellation
     *
     * @return \snac\data\Subject[] Subjects
     *
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * Get the first nationality associated with this constellation
     *
     * @return \snac\data\Nationality nationality
     *
     */
    public function getNationality()
    {
        if (count($this->nationalities) > 0)
            return $this->nationalities[0];
        else
            return null;
    }

    /**
     * Get all nationalities associated with this constellation 
     *
     * @return \snac\data\Nationality[] nationalities
     *
     */
    public function getNationalities()
    {
        return $this->nationalities;
    }

    /**
     * Get the gender for this constellation.  If there are multiple, this
     * will return the first gender in the list.
     *
     * @return \snac\data\Gender First Gender stored for this constellation
     *
     */
    public function getGender()
    {
        if (count($this->genders) > 0)
            return $this->genders[0];
        else
            return null;
    }

    /**
     * Get all genders associated with this constellation. 
     *
     * @return \snac\data\Gender[] all genders
     *
     */
    public function getGenders()
    {
        return $this->genders;
    }

    /**
     * Get all the general contexts text/xml for this constellation
     *
     *
     * @return \snac\data\GeneralContext[]  General Contexts text/xml
     *
     */
    public function getGeneralContexts()
    {
        return $this->generalContexts;
    }

    /**
     * Get the structureOrGenealogies for this constellation 
     *
     * @return \snac\data\StructureOrGenealogy[] list of Structure Or Genealogy information
     *
     */
    public function getStructureOrGenealogies()
    {
        return $this->structureOrGenealogies;
    }

    /**
     * Get the mandates for this constellation 
     *
     * @return \snac\data\Mandate[] list of Mandates
     *
     */
    public function getMandate()
    {
        return $this->mandate;
    }

    /**
     * Returns this object's data as an associative array
     *
     * @param boolean $shorten optional Whether or not to include null/empty components
     * @return string[][] This objects data in array form
     */
    public function toArray($shorten = true) {
        $return = array(
            "dataType" => "Constellation",
            "ark" => $this->ark,
            "entityType" => $this->entityType == null ? null : $this->entityType->toArray($shorten),
            "otherRecordIDs" => array(), 
            "maintenanceStatus" => $this->maintenanceStatus,
            "maintenanceAgency" => $this->maintenanceAgency,
            "maintenanceEvents" => array(),
            "sources" => array(),
            "legalStatuses" => array(), 
            "conventionDeclarations" => array(),
            "languagesUsed" => array(),
            "nameEntries" => array(),
            "occupations" => array(),
            "biogHists" => array(),
            "relations" => array(),
            "resourceRelations" => array(),
            "functions" => array(),
            "places" => array(),
            "subjects" => array(),
            "nationalities" => array(),
            "genders" => array(),
            "generalContexts" => array(),
            "structureOrGenealogies" => array(),
            "mandates" => array()
        );
        
        foreach ($this->mandates as $i => $v)
            $return["mandates"][$i] = $v->toArray($shorten);

        foreach ($this->structureOrGenealogies as $i => $v)
            $return["structureOrGenealogies"][$i] = $v->toArray($shorten);

        foreach ($this->generalContexts as $i => $v)
            $return["generalContexts"][$i] = $v->toArray($shorten);

        foreach ($this->biogHists as $i => $v)
            $return["biogHists"][$i] = $v->toArray($shorten);

        foreach ($this->conventionDeclarations as $i => $v)
            $return["conventionDeclarations"][$i] = $v->toArray($shorten);

        foreach ($this->nationalities as $i => $v)
            $return["nationalities"][$i] = $v->toArray($shorten);

        foreach ($this->otherRecordIDs as $i => $v)
            $return["otherRecordIDs"][$i] = $v->toArray($shorten);

        foreach ($this->maintenanceEvents as $i => $v)
            $return["maintenanceEvents"][$i] = $v->toArray($shorten);
        
        foreach ($this->languagesUsed as $i => $v)
            $return["languagesUsed"][$i] = $v->toArray($shorten);

        foreach ($this->legalStatuses as $i => $v)
            $return["legalStatuses"][$i] = $v->toArray($shorten);

        foreach ($this->sources as $i => $v)
            $return["sources"][$i] = $v->toArray($shorten);

        foreach ($this->genders as $i => $v)
            $return["genders"][$i] = $v->toArray($shorten);

        foreach ($this->nameEntries as $i => $v)
            $return["nameEntries"][$i] = $v->toArray($shorten);

        foreach ($this->occupations as $i => $v)
            $return["occupations"][$i] = $v->toArray($shorten);

        foreach ($this->relations as $i => $v)
            $return["relations"][$i] = $v->toArray($shorten);

        foreach ($this->resourceRelations as $i => $v)
            $return["resourceRelations"][$i] = $v->toArray($shorten);

        foreach ($this->functions as $i => $v)
            $return["functions"][$i] = $v->toArray($shorten);

        foreach ($this->places as $i => $v)
            $return["places"][$i] = $v->toArray($shorten);
        
        foreach ($this->subjects as $i => $v)
            $return["subjects"][$i] = $v->toArray($shorten);
            
        $return = array_merge($return, parent::toArray($shorten));
        
        // Shorten if necessary
        if ($shorten) {
            $return2 = array();
            foreach ($return as $i => $v)
                if ($v != null && !empty($v))
                    $return2[$i] = $v;
            unset($return);
            $return = $return2;
        }

        return $return;
    }

    /**
     * Replaces this object's data with the given associative array
     *
     * Need docs about the keys in the array passed to this. This is called by the AbstractData class
     * constructor which was called by this class' constructor via parent::__construct($data);
     * 
     * @param string[][] $data This objects data in array form
     * @return boolean true on success, false on failure
     */
    public function fromArray($data) {
        if (!isset($data["dataType"]) || $data["dataType"] != "Constellation")
            return false;

        parent::fromArray($data);
            
        unset($this->ark);
        if (isset($data["ark"]))
            $this->ark = $data["ark"];
        else
            $this->ark = null;

        unset($this->entityType);
        if (isset($data["entityType"]))
            $this->entityType = new \snac\data\Term($data["entityType"]);
        else
            $this->entityType = null;

        unset($this->otherRecordIDs);
        $this->otherRecordIDs = array();
        if (isset($data["otherRecordIDs"]))
            foreach ($data["otherRecordIDs"] as $i => $entry)
                $this->otherRecordIDs[$i] = new \snac\data\SameAs($entry);

        unset($this->maintenanceStatus);
        if (isset($data["maintenanceStatus"]))
            $this->maintenanceStatus = $data["maintenanceStatus"];
        else
            $this->maintenanceStatus = null;

        unset($this->maintenanceAgency);
        if (isset($data["maintenanceAgency"]))
            $this->maintenanceAgency = $data["maintenanceAgency"];
        else
            $this->maintenanceAgency = null;

        unset($this->sources);
        $this->sources = array();
        if (isset($data["sources"]))
            foreach ($data["sources"] as $i => $entry)
                $this->sources[$i] = new Source($entry);

        unset($this->legalStatuses);
        $this->legalStatuses = array();
        if (isset($data["legalStatuses"]))
            foreach ($data["legalStatuses"] as $i => $entry)
                $this->legalStatuses[$i] = new LegalStatus($entry);

        unset($this->conventionDeclarations);
        $this->conventionDeclarations = array();
        if (isset($data["conventionDeclarations"]))
            foreach ($data["conventionDeclarations"] as $i => $entry)
                $this->conventionDeclarations[$i] = new \snac\data\ConventionDeclaration($entry);

        unset($this->languagesUsed);
        $this->languagesUsed = array();
        if (isset($data["languagesUsed"]))
            foreach ($data["languagesUsed"] as $i => $entry)
                $this->languagesUsed[$i] = new Language($entry);

        unset($this->biogHists);
        $this->biogHists = array();
        if (isset($data["biogHists"]))
            foreach ($data["biogHists"] as $i => $entry)
                $this->biogHists[$i] = new BiogHist($entry);

        unset($this->subjects);
        $this->subjects = array();
        if (isset($data["subjects"]))
            foreach ($data["subjects"] as $i => $entry)
                $this->subjects[$i] = new Subject($entry);

        unset($this->nationalities);
        $this->nationalities = array();
        if (isset($data["nationalities"]))
            foreach ($data["nationalities"] as $i => $entry)
                $this->nationalities[$i] = new Nationality($entry);

        unset($this->genders);
        $this->genders = array();
        if (isset($data["genders"]))
            foreach ($data["genders"] as $i => $entry)
                $this->genders[$i] = new Gender($entry);

        unset($this->generalContexts);
        $this->generalContexts = array();
        if (isset($data["generalContexts"]))
            foreach ($data["generalContexts"] as $i => $entry)
                $this->generalContexts[$i] = new GeneralContext($entry);

        unset($this->structureOrGenealogies);
        $this->structureOrGenealogies = array();
        if (isset($data["structureOrGenealogies"]))
            foreach ($data["structureOrGenealogies"] as $i => $entry)
                $this->structureOrGenealogies[$i] = new StructureOrGenealogy($entry);

        unset($this->mandates);
        $this->mandates = array();
        if (isset($data["mandates"]))
            foreach ($data["mandates"] as $i => $entry)
                $this->mandates[$i] = new Mandate($entry);

        unset($this->maintenanceEvents);
        $this->maintenanceEvents = array();
        if (isset($data["maintenanceEvents"])) {
            foreach ($data["maintenanceEvents"] as $i => $entry)
                $this->maintenanceEvents[$i] = new MaintenanceEvent($entry);
        }

        unset($this->nameEntries);
        $this->nameEntries = array();
        if (isset($data["nameEntries"])) {
            foreach ($data["nameEntries"] as $i => $entry)
                $this->nameEntries[$i] = new NameEntry($entry);
        }

        unset($this->occupations);
        $this->occupations = array();
        if (isset($data["occupations"])) {
            foreach ($data["occupations"] as $i => $entry)
                $this->occupations[$i] = new Occupation($entry);
        }

        unset($this->relations);
        $this->relations = array();
        if (isset($data["relations"])) {
            foreach ($data["relations"] as $i => $entry)
                $this->relations[$i] = new ConstellationRelation($entry);
        }

        unset($this->resourceRelations);
        $this->resourceRelations = array();
        if (isset($data["resourceRelations"])) {
            foreach ($data["resourceRelations"] as $i => $entry)
                $this->resourceRelations[$i] = new ResourceRelation($entry);
        }

        unset($this->functions);
        $this->functions = array();
        if (isset($data["functions"])) {
            foreach ($data["functions"] as $i => $entry)
                $this->functions[$i] = new SNACFunction($entry);
        }

        unset($this->places);
        $this->places = array();
        if (isset($data["places"])) {
            foreach ($data["places"] as $i => $entry)
                $this->places[$i] = new Place($entry);
        }

        return true;
    }

    /**
     * Set the ARK ID
     *
     * @param string $ark Ark ID for this constellation
     */
    public function setArkID($ark) {

        $this->ark = $ark;
    }

    /**
     * Set Entity type
     *
     * @param \snac\data\Term $type Entity type
     */
    public function setEntityType(\snac\data\Term $type) {

        $this->entityType = $type;
    }

    /**
     * Adds an alternate record id
     *
     * @param \snac\data\SameAs $other The other record ID in a SameAs object
     */
    public function addOtherRecordID(\snac\data\SameAs $other) {

        array_push($this->otherRecordIDs, $other); 
    }

    /**
     * Set maintenance status
     *
     * @param string $status status
     */
    public function setMaintenanceStatus($status) {

        $this->maintenanceStatus = $status;
    }

    /**
     * Set maintenance agency
     *
     * @param string $agency agency
     */
    public function setMaintenanceAgency($agency) {

        $this->maintenanceAgency = $agency;
    }

    /**
     * Adds a source to the list of sources for this constellation
     *
     * @param \snac\data\Source $source Source to add 
     */
    public function addSource(\snac\data\Source $source) {

        array_push($this->sources, $source);
    }

    /**
     * Add a maintenance event
     *
     * @param \snac\data\MaintenanceEvent $event Event to add
     */
    public function addMaintenanceEvent($event) {

        array_push($this->maintenanceEvents, $event);
    }

    /**
     * Add a convention declaration
     *
     * @param \snac\data\ConventionDeclaration $declaration Convention Declaration
     */
    public function addConventionDeclaration($declaration) {

        array_push($this->conventionDeclarations,  $declaration);
    }

    /**
     * Adds a name entry to the known entries for this constellation
     *
     * @param \snac\data\NameEntry $nameEntry Name entry to add
     */
    public function addNameEntry($nameEntry) {

        array_push($this->nameEntries, $nameEntry);
    }

    /**
     * Add biogHist entry
     *
     * @param string $biog BiogHist to add
     */
    public function addBiogHist($biog) {

        array_push($this->biogHists, $biog);
    }

    /**
     * Add occupation
     *
     * @param \snac\data\Occupation $occupation Occupation to add
     */
    public function addOccupation($occupation) {

        array_push($this->occupations, $occupation);
    }

    /**
     * Add function
     *
     * @param \snac\data\SNACFunction $function Function object
     */
    public function addFunction($function) {

        array_push($this->functions, $function);
    }

    /**
     * Add Language Used by this constellation's identity. (You might be tempted to call this
     * setLanguagesUsed() or the singular setLanguageUsed() as the converse of getLanguagesUsed().)
     *
     * @param  \snac\data\Language Language and script used by this identity
     */
    public function addLanguageUsed(\snac\data\Language $language) {
        array_push($this->languagesUsed, $language);
    }

    /**
     * Calls addLanguageUsed() and serves as an alias in DBUtil.
     *
     * In retrospect it doesn't help that much because DBUtil populateLanguage() needs to test the class
     * regardless due to api inconsistency. 
     *
     * Add a language used by this constellation's identity. (You might be tempted to call this
     * setLanguages() or the singular setLanguage() as the converse of getLanguages().)
     *
     * @param  \snac\data\Language Language and script used by this identity
     *
     */ 
    public function addLanguage(\snac\data\Language $language) {
        $this->addLanguageUsed($language);
    }



    /**
     * Add the subject to this Constellation
     *
     * @param \snac\data\Subject $subject Subject to add.
     */
    public function addSubject(\snac\data\Subject $subject) {
        array_push($this->subjects, $subject);
    }

    /**
     * Add a nationality to this Constellation
     *
     * @param \snac\data\Nationality $nationality Nationality
     */
    public function addNationality(\snac\data\Nationality $nationality) {
        array_push($this->nationalities, $nationality);
    }

    /**
     * Add a gender of this Constellation
     *
     * @param \snac\data\Gender $gender Gender to set
     */
    public function addGender($gender) {
        array_push($this->genders, $gender);
    }

    /**
     * Set the gender of this Constellation to be this sole gender.
     * Removes all the other genders
     *
     * @param \snac\data\Gender $gender Gender to set
     */
    public function setGender($gender) {
        unset($this->genders);
        $this->genders = array();
        array_push($this->genders, $gender);
    }

    /**
     * Add relation to another constellation
     *
     * @param \snac\data\ConstellationRelation $relation Relation object defining the relationship
     */
    public function addRelation($relation) {

        array_push($this->relations, $relation);
    }

    /**
     * Add relation to a resource
     *
     * @param \snac\data\ResourceRelation $relation Relation object defining the relationship
     */
    public function addResourceRelation($relation) {

        array_push($this->resourceRelations, $relation);
    }

    /**
     * Add a place to the constellation
     * 
     * @param \snac\data\Place $place Place to add
     */
    public function addPlace(\snac\data\Place $place) {

        array_push($this->places, $place);
    }
    
    /**
     * Add a general context for this constellation
     * 
     * @param \snac\data\GeneralContext $context General context
     */
    public function addGeneralContext($context) {
        array_push($this->generalContexts, $context);
    }
    
    /**
     * Add a structure or genealogy for this constellation
     * 
     * @param \snac\data\StructureOrGenealogy $structure StructureOrGenealogy information
     */
    public function addStructureOrGenealogy($structure) {
        array_push($this->structureOrGenealogies, $structure);
    }
    
    /**
     * Add a legal status to this constellation
     * 
     * @param \snac\data\LegalStatus $legalStatus The legal status to add 
     */
    public function addLegalStatus($legalStatus) {
        array_push($this->legalStatuses, $legalStatus);
    }
    
    /**
     * Add a mandate of this constellation
     * 
     * @param \snac\data\Mandate $mandate Mandate information
     */
    public function addMandate($mandate) {
        array_push($this->mandates, $mandate);
    }
}
