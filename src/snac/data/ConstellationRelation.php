<?php
/**
 * Constellation Relation File
 *
 * Contains the data class for the constellation relations.
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
 * Constellation Relationship.  See the abstract parent class for common methods setDBInfo() and getDBInfo().
 *
 * Data class to store the information about a relationship between Constellations
 *
 * @author Robbie Hott
 *        
 */
class ConstellationRelation extends AbstractData {

    /**
     * Source Constellation ID
     * 
     * Postgres ID (source)
     * 
     * @var int Source constellation ID
     */
    private $sourceConstellation = null;

    /**
     * Target Constellation ID
     * 
     * Postgres ID (target)
     * 
     * @var int Target constellation ID
     */
    private $targetConstellation = null;

    /**
     * ArkID of source
     * 
     * @var string Source constellation ARK ID
     */
    private $sourceArkID = null;

    /**
     * ArkID of the target
     * 
     * From EAC-CPF tag(s):
     * 
     * * cpfRelation/@href
     * 
     * @var string Target constellation ARK ID
     */
    private $targetArkID = null;
    
    /**
     * Entity type of the target
     * 
     * From EAC-CPF tag(s):
     * 
     * * cpfRelation/@role
     * 
     * @var \snac\data\Term Target entity type
     */
    private $targetEntityType = null;

    /**
     * Type of the relation
     * 
     * From EAC-CPF tag(s):
     * 
     * * cpfRelation/@arcrole
     * 
     * @var \snac\data\Term Type of the constellation
     */
    private $type = null;

    /**
     * Alternate Type (unused)
     * 
     * From EAC-CPF tag(s):
     * 
     * * cpfRelation/@type cpfRelation@xlink:type
     *
     * The only value this ever has is "simple". Daniel says not to save it, and implicitly hard code when
     * serializing export.
     * 
     * @var \snac\data\Term Alternate type
     */
    private $altType = null;
    
    /**
     * Type of the relation
     * 
     * From EAC-CPF tag(s):
     * 
     * * cpfRelation/@cpfRelationType
     * 
     * @var \snac\data\Term CPF Relation Type
     */
    private $cpfRelationType = null;

    /**
     * Content in the relation tag
     * 
     * From EAC-CPF tag(s):
     * 
     * * cpfRelation/relationEntry
     * 
     * @var string Content of the relation
     */
    private $content = null;
    
    /**
     * Descriptive Note
     * 
     * Descriptive note for the relation. 
     * 
     * From EAC-CPF tag(s):
     * 
     * * cpfRelation/descriptiveNote
     * 
     * @var string Note attached to relation
     */
    private $note = null;
    
    /**
     * Constructor
     */
    public function __construct($data) {
        $this->setMaxDateCount(1);
        parent::__construct($data);
    }

    /**
     * Get the Source Constellation's ID 
     *
     * @return int Source constellation ID
     *
     */
    function getSourceConstellation()
    {
        return $this->sourceConstellation;
    }
        
    /**
     * Get the Target Constellation's ID 
     *
     * @return int Source constellation ID
     *
     */
    function getTargetConstellation()
    {
        return $this->targetConstellation;
    }
            
    /**
     * Get the Source Constellation's ARK ID 
     *
     * @return string Source constellation ARK ID
     *
     */
    function getSourceArkID()
    {
        return $this->sourceArkID;
    }
    
    /**
     * Get the Target Constellation's ARK ID 
     *
     * @return string Target constellation ARK ID
     *
     */
    function getTargetArkID()
    {
        return $this->targetArkID;
    }

    /**
     * Get the Target Constellation's Entity Type 
     *
     * * cpfRelation/@role
     *
     * @return \snac\data\Term Target entity type
     *
     */
    function getTargetEntityType()
    {
        return $this->targetEntityType;
    }

    /**
     * Get the type of this relation (such as sameAs, correspondedWith, etc)
     *
     * * cpfRelation/@arcrole
     *
     * @return \snac\data\Term Type of the constellation
     *
     */
    function getType()
    {
        return $this->type;
    }

    /**
     * Get the xlink type. 
     * 
     * This should not be used, as xlink type should always be simple
     * 
     * * cpfRelation/@type cpfRelation@xlink:type
     *
     * The only value this ever has is "simple". Daniel says not to save it, and implicitly hard code when
     * serializing export.
     * 
     * @return \snac\data\Term Alternate type
     *
     */
    function getAltType()
    {
        return $this->altType;
    }

    /**
     * Get the secondary Relation type
     * 
     * ANF Used this as a second way of describing
     * the normal relation type.  That is, "associative" for "associatedWith", and "temporal-after"
     * for "isSucceededBy" 
     *
     * * cpfRelation/@cpfRelationType
     * 
     * @return \snac\data\Term CPF Relation Type
     *
     */
    function getCpfRelationType()
    {
        return $this->cpfRelationType;
    }

    /**
     * Get the text/xml content of this relation 
     *
     * * cpfRelation/relationEntry
     * 
     * @return string Content of the relation
     *
     */
    function getContent()
    {
        return $this->content;
    }

    /**
     * Get descriptive note
     * 
     * Get the human readable descriptive note for this relation 
     *
     * * cpfRelation/descriptiveNote
     * 
     * @return string Note attached to relation
     *
     */
    function getNote()
    {
        return $this->note;
    }

    /**
     * Returns this object's data as an associative array
     *
     * @param boolean $shorten optional Whether or not to include null/empty components
     * @return string[][] This objects data in array form
     */
    public function toArray($shorten = true) {
        $return = array(
            "dataType" => "ConstellationRelation",
            "sourceConstellation" => $this->sourceConstellation,
            "targetConstellation" => $this->targetConstellation,
            "sourceArkID" => $this->sourceArkID,
            "targetArkID" => $this->targetArkID,
            "targetEntityType" => $this->targetEntityType == null ? null : $this->targetEntityType->toArray($shorten),
            "type" => $this->type == null ? null : $this->type->toArray($shorten),
            "altType" => $this->altType == null ? null : $this->type->toArray($shorten),
            "cpfRelationType" => $this->cpfRelationType == null ? null : $this->type->toArray($shorten),
            "content" => $this->content,
            "note" => $this->note
        );
            
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
     * @param string[][] $data This objects data in array form
     * @return boolean true on success, false on failure
     */
    public function fromArray($data) {
        if (!isset($data["dataType"]) || $data["dataType"] != "ConstellationRelation")
            return false;

        parent::fromArray($data);

        if (isset($data["sourceConstellation"]) && $data["sourceConstellation"] != null)
            $this->sourceConstellation = $data["sourceConstellation"];
        else
            $this->sourceConstellation = null;

        if (isset($data["targetConstellation"]) && $data["targetConstellation"] != null)
            $this->targetConstellation = $data["targetConstellation"];
        else
            $this->targetConstellation = null;

        if (isset($data["sourceArkID"]) && $data["sourceArkID"] != null)
            $this->sourceArkID = $data["sourceArkID"];
        else
            $this->sourceArkID = null;

        if (isset($data["targetArkID"]) && $data["targetArkID"] != null)
            $this->targetArkID = $data["targetArkID"];
        else
            $this->targetArkID = null;

        if (isset($data["targetEntityType"]) && $data["targetEntityType"] != null)
            $this->targetEntityType = new \snac\data\Term($data["targetEntityType"]);
        else
            $this->targetEntityType = null;

        if (isset($data["type"]) && $data["type"] != null)
            $this->type = new \snac\data\Term($data["type"]);
        else
            $this->type = null;

        if (isset($data["altType"]) && $data["altType"] != null)
            $this->altType = new \snac\data\Term($data["altType"]);
        else
            $this->altType = null;

        if (isset($data["cpfRelationType"]) && $data["cpfRelationType"] != null)
            $this->cpfRelationType = new \snac\data\Term($data["cpfRelationType"]);
        else
            $this->cpfRelationType = null;

        if (isset($data["content"]))
            $this->content = $data["content"];
        else
            $this->content = null;

        if (isset($data["note"]))
            $this->note = $data["note"];
        else
            $this->note = null;

        return true;
    }

    /**
     * Set the target constellation numeric id
     *
     * @param int $targetConstellation target constellation database record id number
     */
    public function setTargetConstellation($targetConstellation)
    {
        $this->targetConstellation = $targetConstellation;
    }

    /**
     * Set the target ARK ID
     *
     * @param string $ark Target ARK ID
     */
    public function setTargetArkID($ark) {

        $this->targetArkID = $ark;
    }
    
    /**
     * Set the target entity type
     * 
     * @param \snac\data\Term $type Target's entity type
     */
    public function setTargetType(\snac\data\Term $type) {
        $this->targetEntityType = $type;
    }

    /**
     * Set the relation type
     *
     * @param \snac\data\Term $type Type of the relation
     */
    public function setType(\snac\data\Term $type) {

        $this->type = $type;
    }
    
    /**
     * Set the CPF Relation type
     * 
     * @param \snac\data\Term $type CPF Relation Type
     */
    public function setCPFRelationType(\snac\data\Term $type) {
        $this->cpfRelationType = $type;
    }

    /**
     * Set the relation's alternate type
     *
     * cpfRelation/@type cpfRelation@xlink:type
     *
     * The only value this ever has is "simple". Daniel says not to save it, and implicitly hard code when
     * serializing export.
     *
     * @param \snac\data\Term $type Alternate type of the relation
     */
    public function setAltType(\snac\data\Term $type) {

        $this->altType = $type;
    }

    /**
     * Set the content of the relation
     * 
     * @param string $content Relation content
     */
    public function setContent($content) {

        $this->content = $content;
    }
    
    /**
     * Set the note for this constellation relation
     * 
     * @param string $note Resource note
     */
    public function setNote($note) {

        $this->note = $note;
    }
}
