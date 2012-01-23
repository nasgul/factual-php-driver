<?php
/**
 * Represents the basic concept of a response from Factual.
 * This is a refactoring of the Factual Driver by Aaron: https://github.com/Factual/factual-java-driver
 * @author Tyler
 * @package Factual
 * @license Apache 2.0
 */
abstract class Response {
	/**
	 * Array of entities as objects
	 */
  protected $objects = array(); 
  protected $version = null; //string
  protected $status = null; //string
  protected $totalRowCount = null; //int
  protected $includedRows = null; //int
  protected $data = array();
  protected $json;
  protected $countTotal = null;

  /**
   * Constructor, parses from a JSON response String.
   * @param string json The JSON response String returned by Factual.
   */
  public function __construct($json) {
    $this->json = $json;
    try {
    	//assign data value
    	$rootJSON = json_decode($json,true);
    	$this->data = $rootJSON['response']['data'];
    	//assign status value
    	$this->status = $rootJSON['status'];
    	//assign version
    	$this->version = $rootJSON['version'];
    	//assign total row count
    	if(isset($rootJSON['response']['total_row_count'])){
    		$this->countTotal = $rootJSON['response']['total_row_count'];
    	}
    	if(isset($rootJSON['response']['included_rows'])){
    		$this->includedRows = $rootJSON['response']['included_rows'];
    	}    	
    } catch (Exception $e) {
    	//add note about json encoding borking here
      throw $e;
    }
  }

  /**
   * @return The full JSON response from Factual
   */
  public function getJson() {
    return $this->json;
  }

  /**
   * The status returned by the Factual API server, e.g. "ok".
   * @return status returned by the Factual API server.
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * The version tag returned by the Factual API server, e.g. "3".
   * @return the version tag returned by the Factual API server.
   */
  public function getVersion() {
    return $this->version;
  }

  /**
   * @return total underlying row count, or {@link #UNDEFINED} if unknown.
   */
  public function getTotalRowCount() {
    return $this->totalRowCount;
  }

  /**
   * @return int Count of result rows returned in this response.
   */
  public function getIncludedRowCount() {
    return $this->includedRows;
  }

  /**
   * An array of data returned by Factual. 
   * @return the main data returned by Factual.
   */
  public function getData() {
    return $this->data;
  }

  /**
   * @return int Count of elements of the result set
   */
  public function size() {
	return $this->includedRows;  
  }

  /**
   * @return array The first data record or null if no data was returned.
   */
  public function first() {
    if(empty($this->data)) {
      return null;
    } else {
      return $this->data[0];
    }
  }

  /**
   * Total result count, if specifically requested by Query::includeRowCount()
   * @return int (Null == not requested)
   */
  public function getRowCount() {
    return $this->countTotal;
  }

  /**
   * @return true if Factual's response did not include any results records for
   *         the query, false otherwise.
   */
  public function isEmpty() {
    return $this->includedRows == 0;
  }

  /**
   * Subclasses of Response must provide access to the original JSON
   * representation of Factual's response.
   * 
   * @return the original JSON representation of Factual's response.
   */
  public function toString() {
    return $this->getJson();
  }
  
   /**
  * Results as array of objects
  * @param string type Entity type: Place, Crosswalk
  * @return array Array of objects
  */
	public function getObjects($type){
		if (is_array($this->data)){
			foreach ($this->data as $entity){
				$this->objects[] = new $type($entity);
			}
			return $this->objects;
		} else {
			return array();
		}
	}

}
?>

