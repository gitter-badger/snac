<?php
/*
 * Rest API Class File
 *
 * Contains the main REST interface class that instantiates the REST UI
 *
 * License:
 * 
 *
 * @author Robbie Hott
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @copyright 2015 the Rector and Visitors of the University of Virginia, and 
 * the Regents of the University of California
 */
namespace snac\client\rest;


/**
 * Rest Class
 *
 * This is the main REST user interface class.  It should be instantiated, then the run() 
 * method called to start the rest api handler.
 *
 * @author Robbie Hott
 */


class Rest {


	/**
	 * Input parameters from the querier
	 * @var array Associative array of the input query
	 */
	private $input = null;
	
	/**
	 * Headers for response
	 * @var array Response headers
	 */
	private $responseHeaders = array("Content-Type: application/json");
	
	/**
	 * Response text
	 * @var string response
	 */
	private $response = "";
	
	/**
	 * Constructor
	 *
	 * Requires the input to the server as an associative array
	 * @param array $input Input to the server
	*/
	public function __construct($input) {
		$this->input = $input;
	}
	
	/**
	 * Run Method
	 *
	 * Starts the server
	 */
	public function run() {
		
		// server url
		$url = "http://localhost:8081";
		
		//Convert input to JSON
		$data = json_encode($this->input);
		
		// Use CURL to send request to the internal server
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
				'Content-Length: ' . strlen($data)));
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response  = curl_exec($ch);
		curl_close($ch);
		
		$this->response = $response;
		
		return;
	}
	
	/**
	 * Get Response Headers
	 *
	 * Returns the headers for the server's return value.  This will likely
	 * usually be the JSON content header.
	 * @return array headers for output
	 */
	public function getResponseHeaders() {
		return $this->responseHeaders;
	}
	
	/**
	 * Get Return Statement
	 *
	 * This should compile the Server's response statement.  Currently, it returns a
	 * JSON-encoded string or other content value that can be echoed to generate the
	 * appropriate response. This should usually be JSON, but may be the contents of a file
	 * to be downloaded by the user, so we leave it flexible rather than returning an
	 * associative array.
	 *
	 * @return string server response appropriately encoded
	 */
	public function getResponse() {
		// TODO: Fill in body
		 
		return $this->response;
	}
}

