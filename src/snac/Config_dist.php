<?php
/**
 * Configuration File 
 *
 * Contains the configuration options for this instance of the server
 *
 * License:
 * 
 *
 * @author Robbie Hott
 * @license http://opensource.org/licenses/BSD-3-Clause BSD 3-Clause
 * @copyright 2015 the Rector and Visitors of the University of Virginia, and 
 * the Regents of the University of California
 */
namespace snac;

/**
 * Configuration class
 * 
 * This class contains all the configuration variables for the entire system.  It makes use of only
 * public static fields that may be read by any other class in the system.  We use this to better scope
 * the configuration settings and avoid global variables and constants.
 * 
 * @author Robbie Hott
 *
 */
class Config {

	
	/**
	 * @var string URL of the back-end server
	 */
	public static $INTERNAL_SERVERURL = "http://localhost:82";
	
}