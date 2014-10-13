<?php

class BarChart extends Chart {
	
	/**
	 * Construct class for BarChart class
	 */
	function __construct($width, $height, $id = 'flot_chart_placeholder' ,$additional_tags = null) {
		parent::__construct($width, $height, $id, $additional_tags);
	} //__construct
	
	function render($date_mode = false) {
		
		$mode = ($date_mode) ? 'time' : null;
		$tick_size = ($date_mode) ? array(1,'day') : 1;
		
	  $this->setOption('xaxis', array(
      'mode' => $mode,
			'minTickSize'    => $tick_size,
      'tickColor'   => '#E4E4E4',
	  	'min'					=> $this->getMinValue('x') - (60*60*24*1000),
	  	'max'					=> $this->getMaxValue('x') + (60*60*24*1000)
    ));
		
    $this->setOption('colors', array('#e5c65a','#95534d','#a7ab9a','#c9c9c8','#dedeb6','#d0d4c2','#f3dc8c','#fff9e2','#cfa39f','#e5d9c1','#c1ae8a'));
    
		$this->options['series'] = array(
			'stack' => true,
			'bars'	=> array(
				'show' 		 => true,
				'barWidth' => 1000 * 60 * 60 * 12,
				'align' 	 => 'center',
				'lineWidth'=> 0
			)
		);
		return parent::render();
	} //
	
} //BarChart