<?php

/**
 * Implemetation of one chart point 
 *
 * @package angie.frameworks.envrionment
 * @subpackage models
 * @author Goran Blazin
 * 
 */


class ChartPoint {
	
	/**
	 * This value represents value of point on x axis 
	 * 
	 * @var decimal
	 */
	protected $x_value = 0;
	
	/**
	 * This value represents value of point on y axis 
	 * 
	 * @var decimal
	 */
	protected $y_value = 0;
	
	/**
	 * Constructor for ChartPoint
	 * 
	 * @param decimal $x_value
	 * @param decimal $y_value
	 */
	function __construct($x_value = 0, $y_value = 0) {
		if (!is_numeric($x_value)) {
			throw new InvalidParamError('x_value', $x_value, 'x_value myst be a numerical value');
		} elseif (!is_numeric($x_value)) {
			throw new InvalidParamError('y_value', $y_value, 'y_value myst be a numerical value');
		} else {
			$this->x_value = $x_value;
			$this->y_value = $y_value;
		}//if
	} //__construct
	
	/**
	 * Sets x value for this point
	 * 
	 * @param decimal $x_value
	 * @throws InvalidParamError
	 * @return true
	 */
	function setX($x_value) {
		if (!is_numeric($x_value)) {
			throw new InvalidParamError('x_value', $x_value, 'x_value myst be a numerical value');
		} else {
			$this->x_value = $x_value;
		} //if
		return true;
	} //setX
	
	/**
	 * Sets y value for this point
	 * 
	 * @param decimal $y_value
	 * @throws InvalidParamError
	 * @return true
	 */
	function setY($y_value) {
		if (!is_numeric($y_value)) {
			throw new InvalidParamError('y_value', $y_value, 'y_value myst be a numerical value');
		} else {
			$this->y_value = $y_value;
		} //if
		return true;
	} //setY
	
	/**
	 * Gets X value
	 * 
	 * @return decimal
	 */
	function getX() {
		return $this->x_value;
	} //getX
	
	/**
	 * Gets Y value
	 * 
	 * @return decimal
	 */
	function getY() {
		return $this->y_value;
	} //getY
	
	/**
	 * Return point as array with 2 numbers
	 * 
	 * @return array of decimal
	 */
	function getPointAsArray() {
		return array($this->x_value,$this->y_value);
	} //getPoint
	
} //ChartPoint

