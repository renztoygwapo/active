<?php

class PieChart extends Chart {
	
	function __construct($width, $height, $id = 'flot_chart_placeholder' ,$additional_tags = null) {
		parent::__construct($width, $height, $id, $additional_tags);
	} //__construct
	
	function render() {
		
		$this->options['series'] = array(
			'pie' => array(
				'show' => true
			)
		);
		$this->setOption('legend', array('show' => false));
		
		$this->setOption('colors', array('#e5c65a','#95534d','#a7ab9a','#c9c9c8','#dedeb6','#d0d4c2','#f3dc8c','#fff9e2','#cfa39f','#e5d9c1','#c1ae8a'));
		
		return parent::render();
	} // render

  /**
   * Method that shortens a string to fit the legend in pie chart
   *
   * @param $string String for shortening
   *
   * @return string
   */
  static function makeShortForPieChart($string) {
    return strlen($string) > 20 ? substr($string,0,16) . '...' : $string;
  } //makeShortForPieChart
} //PieChart