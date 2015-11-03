<?php

/**
 * Snac Function File
 *
 * Contains the data class for functions
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
 * Function data storage class
 * 
 * @author Robbie Hott
 *        
 */
class SNACFunction extends AbstractData {

    /**
     *
     * @var string Function controlled vocabulary term
     */
    private $term;

    /**
     *
     * @var string Type of the function
     */
    private $type;

    /**
     *
     * @var \snac\data\SNACDate Date range of the function
     */
    private $dates;

    /**
     *
     * @var string Descriptive note for the function
     */
    private $note;

    /**
     *
     * @var string Vocabulary source for the function
     */
    private $vocabularySource;
    
    /**
     * Returns this object's data as an associative array
     *
     * @return string[][] This objects data in array form
     */
    public function toArray() {
        $return = array(
            "dataType" => "SNACFunction",
            "term" => $this->term,
            "type" => $this->type,
            "dates" => $this->dates == null ? null : $this->dates->toArray(),
            "vocabularySource" => $this->vocabularySource,
            "note" => $this->note
        );
        return $return;
    }

    /**
     * Replaces this object's data with the given associative array
     *
     * @param string[][] $data This objects data in array form
     * @return boolean true on success, false on failure
     */
    public function fromArray($data) {
        if (!isset($data["dataType"]) || $data["dataType"] != "SNACFunction")
            return false;

        if (isset($data["term"]))
            $this->term = $data["term"];
        else
            $this->term = null;

        if (isset($data["type"]))
            $this->type = $data["type"];
        else
            $this->type = null;

        if (isset($data["dates"]))
            $this->dates = new SNACDate($data["dates"]);
        else
            $this->dates = null;

        if (isset($data["vocabularySource"]))
            $this->vocabularySource = $data["vocabularySource"];
        else
            $this->vocabularySource = null;

        if (isset($data["note"]))
            $this->note = $data["note"];
        else
            $this->note = null;

        return true;

    }

    /**
     * Set the term of this function (controlled vocabulary)
     * 
     * @param string $term term
     */
    public function setTerm($term) {

        $this->term = $term;
    }

    /**
     * Set the type of this function
     * 
     * @param string $type type
     */
    public function setType($type) {

        $this->type = $type;
    }

    /**
     * Set the date range
     *
     * @param \snac\data\SNACDate $date Date object for the range
     */
    public function setDateRange($date) {

        $this->dates = $date;
    }

    /**
     * Set the vocabulary source
     *
     * @param string $vocab Vocabulary source string
     */
    public function setVocabularySource($vocab) {

        $this->vocabularySource = $vocab;
    }

    /**
     * Set the descriptive note for this function
     *
     * @param string $note Descriptive note string
     */
    public function setNote($note) {

        $this->note = $note;
    }
}
