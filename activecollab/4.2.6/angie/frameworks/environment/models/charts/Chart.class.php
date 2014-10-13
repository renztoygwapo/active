<?php

	/**
   * Foundation implementation of all charts object
   *
   * @package angie.frameworks.envrionment
   * @subpackage models
   * @author Goran Blazin
   * 
   */

abstract class Chart {
	
	/**
	 * String that holds html div tag which will contain the chart 
	 * 
	 * @var string
	 */
	protected $placeholder;
	
	/**
	 * Id of the placeholder
	 * 
	 * @var string
	 */
	protected $placeholder_id;
	
	/**
	 * Array that holds all the chart series
	 * 
	 * @var array of ChartSerie
	 */
	protected $series = array();
	
	/**
	 * String that holds html div tag which could contain the legend 
	 * 
	 * @var string
	 */
	protected $legend_placeholder = '';
	
	/**
	 * Array that holds all the chart options
	 * 
	 * @var array
	 */
	protected $options = array();
	
	
	/**
	 * Constructor method that set the attributes, id, height and width of the chart div container
	 * 
	 * @param string $width
	 * @param string $height
	 * @param string $id
	 * @param mixed $additional_tags 
	 * 
	 * @return bool
	 */
	
	function __construct($width, $height, $id = 'flot_chart_placeholder' ,$additional_tags = null) {
		if (empty($id) || !is_string($id)) {
			throw new ErrorException('$id value must not be non-empty string');
		} //if
		$style = 'style="width: ' . $width . '; height: ' . $height . '"';
		$tags = "";
		if ($additional_tags && is_foreachable($additional_tags)) {
			foreach ($additional_tags as $name => $value) {
				if ($name != 'id') {
					$tags .= " $name = '$value'";
				} //if
			} //foreach
		} //if
		$this->placeholder_id = $id;
		$tags .= "id = \"$id\"";
		$this->placeholder = "<div $tags $style></div>";
	} //__construct
	
	/**
	 * Renders the chart
	 */
	function render() {
    AngieApplication::useWidget('flot', ENVIRONMENT_FRAMEWORK);

		if ($this->legend_placeholder) {
			$this->options['legend']['container'] = '#'.$this->placeholder_id . '_legend';
			$this->options['legend']['show'] = true;
		} //if
		
		$chart_series_json_object = '[';
		for ($i = 0; $i < count($this->series); $i++) {
			$chart_series_json_object .= ($i > 0) ? ',' : '';
			$chart_series_json_object .= $this->series[$i]->renderSerie();
		} //for
		$chart_series_json_object .= ']';
		$chart_options_json_object = !empty($this->options) ? ','.json_encode($this->options) : '';
		
		$javascript = '<script type="text/javascript">
			var placeholder = $("#'.$this->placeholder_id.'");
			$(function () { 
				$.plot(placeholder, '.$chart_series_json_object.' '.$chart_options_json_object.');
			});
		</script>';
		return ($this->placeholder . $this->legend_placeholder . "\n" . $javascript);
	} //render
	
	/**
	 * Methods that set the attributes, id, height and width of the legend div container
	 * 
	 * @param string $width
	 * @param string $height
	 * @param mixed $additional_tags 
	 * 
	 * @return bool
	 */
	function setLegendPlaceholder($width = 200, $height = 50 ,$additional_tags = null) {
		$style = 'style="width:' . $width . 'px;height:' . $height . 'px"';
		$tags = "";
		if ($additional_tags && is_foreachable($additional_tags)) {
			foreach ($additional_tags as $name => $value) {
				if ($name != 'id') {
					$tags .= " $name = '$value'";
				} //if
			} //foreach
		} //if
		$id = $this->placeholder_id.'_legend';
		$tags .= "id = \"$id\"";
		$this->legend_placeholder = "<div $tags $style></div>";
		
		return true;
	} //setLegendPlaceholder
	
	/**
	 * Add new serie to the chart
	 * 
	 * @param array of ChartSerie $serie
	 * 
	 * @return bool
	 */
	function addSeries($serie_array) {
		if (!is_foreachable($serie_array)) {
			$serie_array = array($serie_array);
		} //if
		foreach ($serie_array as $serie) {
			if ($serie instanceof ChartSerie) {
				$this->series[] = $serie;
			} else {
				throw new InvalidParamError('serie_array', $serie_array, 'serie_array must be an array of ChartSerie objects');
			} //if
		} //foreach
		return true;
	} //addSeries
	
	
	/**
	 * Removes certain serie from chart if an index is integer. If index is null removes all the series from the chart
	 * 
	 * @param mixed $index
	 * @throws InvalidParamError
	 * @return true
	 */
  function removeSerieByIndex($index = null) {
 	  if ($index === null) {
 	 	  $this->series = array();
 	  } elseif (!is_int($index) || ($index < 0) ) {
 	    throw new InvalidParamError('index', $index, 'index must be an non negative integer');
 	  } else {
 	 	  unset($this->series[$index]);
		  $this->series = array_values($this->series);
 	  } //if
 	  return true;
  } //removeSerieByIndex
  
  /**
   * Removes one serie from the chart if it exists
   * 
   * @param ChartSerie $serie
   */
  function removeSerie(ChartSerie $serie) {
	  if (in_array($serie, $this->series)) {
		  for ($i=0; i < count($this->series); $i++) {
			  if ($this->series[$i] === $serie) {
				  unset($this->series[$i]);
				  $this->series = array_values($this->series);
				  break;
			  } //if
		  } //for
		  return true;
	  } else {
	   return false;
	  } //if
  } //removeSerie
  
  /**
   * Sets a value for a provided option. 
   * 
   * @param string $option_name
   * @param mixed $option_value
   * 
   * @return bool 
   */
	function setOption($option_name, $option_value) {
		$this->options[$option_name] = $option_value;
		return true;
	} //setOption
	
	/**
	 * Function that calculates which days are weekends
	 * 
	 * @param int $first_day
	 * @param int $last_day
	 * 
	 * @return Array
	 */
	protected function chartNonWorkDaysMarkings() {
		
		$workweek_data = ConfigOptions::getValue('time_workdays');
    
		$markings = array();
  	//get first Sunday
  	$i = 0;
  	$day = ($this->getMinValue('x') / 1000) - ( (date('w',($this->getMinValue('x') / 1000)) % 7) * (60 * 60 * 24));
  	do  {
  		if (Globalization::isDayOff(new DateValue($day))) {
  			$markings [] = array ('xaxis' => array ( 'from' => $day * 1000, 'to' => $day*1000 + 24 * 60 * 60 * 1000), 'color' => DAY_OFF_COLOR_CHART);
  		}	elseif (!in_array($i, $workweek_data)) {
  			$markings [] = array ('xaxis' => array ( 'from' => $day * 1000, 'to' => $day*1000 + 24 * 60 * 60 * 1000), 'color' => NON_WORK_DAY_COLOR_CHART);
  		} //if
  		$day += 24 * 60 * 60;
  		$i = ($i === 6) ? 0 : ++$i;
  	} while ($day < ($this->getMaxValue('x') / 1000));
  	return $markings;
	} //chartWeekendMarkings
	
	/**
	 * Returns minimum value on x or y axis
	 * 
	 * @param string $axis
	 * @return int 
	 */
	
	protected function getMinValue($axis) {
		$return_value = null;
		if (strtolower($axis) === 'x') {
		  $function = 'getX';
		} elseif (strtolower($axis) === 'y') {
		  $function = 'getY';
		} else {
		  throw new ErrorException('Value $axis must be "x" or "y"');
		} //if
		
		foreach ($this->series as $serie) {
			if ($serie instanceof ChartSerie) {
				$points = array();
				foreach ($serie->getPoint() as $point) {
					$points[] = $point->$function();
				} //foreach
				$return_value = (is_null($return_value) || min($points) < $return_value) ? min($points) : $return_value;
			} //if
		} //foreach
		return $return_value;
	} //getMinValue
	
	/**
	 * Returns maximum value on x or y axis
	 * 
	 * @param string $axis
	 * @return int 
	 */
	
	protected function getMaxValue($axis) {
		$return_value = null;
		if (strtolower($axis) === 'x') {
		  $function = 'getX';
		} elseif (strtolower($axis) === 'y') {
		  $function = 'getY';
		} else {
		  throw new ErrorException('Value $axis must be "x" or "y"');
		} //if
		
		foreach ($this->series as $serie) {
			if ($serie instanceof ChartSerie) {
				$points = array();
				foreach ($serie->getPoint() as $point) {
					$points[] = $point->$function();
				} //foreach
				$return_value = (is_null($return_value) || max($points) < $return_value) ? max($points) : $return_value;
			} //if
		} //foreach
		return $return_value;
	} //getMinValue
	
} //Charts