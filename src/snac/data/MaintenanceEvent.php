<?php

/**
 * Maintenance Event File
 *
 * Contains the information about an individual maintenance event.
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
 * Maintenance Event Class
 *
 * Data storage class for maintenance events on an identity constellation.
 *
 * @author Robbie Hott
 *        
 */
class MaintenanceEvent extends AbstractData {

    /*
     * maintenanceEvent/eventType 
     * @var string Event type
     */
    private $eventType;

    /**
     * maintenanceEvent/eventDateTime
     * @var string Date and Time string of the event
     */
    private $eventDateTime;

    /**
     * maintenanceEvent/eventDateTime/@standardDateTime
     * @var string Standardized date time of the event
     * 
     */
    private $standardDateTime;
    
    /**
     * maintenanceEvent/agentType
     * @var string Type of the agent performing the event
     */
    private $agentType;

    /**
     * maintenanceEvent/agent
     * @var string Agent that performed the event
     */
    private $agent;

    /**
     * maintenanceEvent/eventDescription
     * @var string Description of the event
     */
    private $eventDescription;
    
    /**
     * Returns this object's data as an associative array
     *
     * @return string[][] This objects data in array form
     */
    public function toArray() {
        $return = array(
            "dataType" => "MaintenanceEvent",
            "eventType" => $this->eventType,
            "eventDateTime" => $this->eventDateTime,
            "standardDateTime" => $this->standardDateTime,
            "agentType" => $this->agentType,
            "agent" => $this->agent,
            "eventDescription" => $this->eventDescription
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
        if (!isset($data["dataType"]) || $data["dataType"] != "MaintenanceEvent")
            return false;

        if (isset($data["eventType"]))
            $this->eventType = $data["eventType"];
        else
            $this->eventType = null;

        if (isset($data["eventDateTime"]))
            $this->eventDateTime = $data["eventDateTime"];
        else
            $this->eventDateTime = null;

        if (isset($data["standardDateTime"]))
            $this->standardDateTime = $data["standardDateTime"];
        else
            $this->standardDateTime = null;

        if (isset($data["agent"]))
            $this->agent = $data["agent"];
        else
            $this->agent = null;

        if (isset($data["agentType"]))
            $this->agentType = $data["agentType"];
        else
            $this->agentType = null;

        if (isset($data["eventDescription"]))
            $this->eventDescription = $data["eventDescription"];
        else
            $this->eventDescription = null;

        return true;
    }

    /**
     * Set the event type.
     * 
     * @param string $eventType Event type
     */
    public function setEventType($eventType) {

        $this->eventType = $eventType;
    }

    /**
     * Set the date and time of the event.
     * 
     * @param string $eventDateTime DateTime string of the event
     */
    public function setEventDateTime($eventDateTime) {

        $this->eventDateTime = $eventDateTime;
    }

    /**
     * Set the standardized date and time of the event.
     * 
     * @param string $eventDateTime DateTime string of the event
     */
    public function setStandardDateTime($eventDateTime) {

        $this->standardDateTime = $eventDateTime;
    }

    /**
     * Set the agent type.
     * 
     * @param string $agentType Agent type
     */
    public function setAgentType($agentType) {

        $this->agentType = $agentType;
    }

    /**
     * Set the agent that performed the event.
     * 
     * @param string $agent Agent
     */
    public function setAgent($agent) {

        $this->agent = $agent;
    }

    /**
     * Set the event description.
     * 
     * @param string $eventDescription Description of the event
     */
    public function setEventDescription($eventDescription) {

        $this->eventDescription = $eventDescription;
    }
}
