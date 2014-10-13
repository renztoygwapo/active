<?php

	/**
   * Implemetation of one chart serie (element)
   *
   * @package angie.frameworks.envrionment
   * @subpackage models
   * @author Goran Blazin
   * 
   */

class ChartSerie {
	
	/**
	 * Array that holds the serie data
	 * 
	 * @var ChartPoint[]
	 */
	public $data = array ();
	
	/**
	 * Array that holds the serie options
	 * 
	 * @var array
	 */
	protected $options = array (
		'label' => null,
		'color' => null,
		'shadowSize' => null,
		'clickable' => false,
    'hoverable' => false,
		'points'	=> array(),
		'lines'	=> array()
	);
	
	/**
	 * Constructor that initialises values for the chart serie
	 * 
	 * $additional_parameters is an associative array with following fields (all optional):
	 * 
	 * - label - label of the serie
	 * - color - color of the serie
	 * - shadowSize - the default size of shadows in pixels
	 * - clickable - boolean
	 * - hoverable - boolean
	 * 
	 * @param array $data_array - ChartPoint or Array of ChartPoint
	 * @param array $additional_parameters
	 */
	function __construct($point_array, $additional_parameters = null) {
		if (!is_foreachable($point_array)) {
			$point_array = array($point_array);
		} //if
		
		foreach ($point_array as $point) {
			if ($point instanceof ChartPoint) {
				$this->data[] = $point;
			} else {
				throw new InvalidParamError('point', $point, '$point must be an array of ChartPoint objects');
			} //if
		} //foreach
		
		if ($additional_parameters !== null && is_foreachable($additional_parameters)) {
			foreach ($this->options as $key => $option) {
				if ($additional_parameters[$key]) {
					$this->options[$key] = $additional_parameters[$key];
				} //if
			} //foreach
		} //if
		
	} //__construct
	
	/**
	 * Adds points to the serie
	 * 
	 * @param ChartPoint or array of ChartPoint $point_array
	 * @throws InvalidParamError
	 * @return true 
	 */
	function addPoints($point_array) {
		if (!is_foreachable($point_array)) {
			$point_array = array($point_array);
		} //if
		foreach ($point_array as $point) {
			if ($point instanceof ChartPoint) {
				$this->data[] = $point;
			} else {
				throw new InvalidParamError('point', $point, '$point must be an array of ChartPoint objects');
			} //if
		} //foreach
		return true;
	} //addPoints
	
	/**
	 * Removes certain point from serie if an index is integer. If index is null removes all the points from the serie
	 * 
	 * @param mixed $index
	 * @throws InvalidParamError
	 * @return true
	 */
  function removePointsByIndex($index = null) {
 	  if ($index === null) {
 	 	  $this->data = array();
 	  } elseif (!is_int($index) || ($index < 0) ) {
 	    throw new InvalidParamError('index', $index, 'index must be an non negative integer');
 	  } else {
 	 	  unset($this->data[$index]);
		  $this->data = array_values($this->data);
 	  } //if
 	  return true;
  } //removePointsByIndex
	
  /**
   * Removes one point from the serie if it exists
   * 
   * @param ChartPoint $point
   */
  function removePoint(ChartPoint $point) {
	  if (in_array($point, $this->data)) {
		  for ($i=0; $i < count($this->data); $i++) {
			  if ($this->data[$i] === $point) {
				  unset($this->data[$i]);
				  $this->data = array_values($this->data);
				  break;
			  } //if
		  } //for
		  return true;
	  } else {
	   return false;
	  } //if
  } //removePoint
  
  /**
   * Get point for index from serie. If index is null it will return array of all points
   * 
   * @param int $index
   * @return mixed
   */
  function getPoint($index = null) {
  	if (is_null($index)) {
  		return $this->data;
  	} else {
  		return $this->data[intval($index)];
  	} //if
  } //getPoint
	
  /**
   * Sets a value for a provided option. Returns true if an option exists, false otherwise.
   * 
   * @param string $option_name
   * @param mixed $option_value
   * 
   * @return bool 
   */
	function setOption($option_name, $option_value) {
		foreach ($this->options as $option => $value) {
			if ($option = $option_name) {
				$this->options[$option] = $option_value;
				return true;
			} //if
		} //foreach
		return false;
	} //setOption

  /**
   * Gets a value for a provided option.
   *
   * @param string $option_name
   * @return mixed
   */
  function getOption($option_name) {
    return $this->options[$option_name];
  } //getOption
	
	function renderSerie() {
		$js_points_array = array();
		foreach($this->data as $point) {
			$js_points_array[] = array($point->getX(),$point->getY());
		} //foreach
		$json_array = array_filter($this->options);
		$json_array['data'] = $js_points_array;
		return(json_encode($json_array));
	} //renderSerie
	
} //ChartSerie