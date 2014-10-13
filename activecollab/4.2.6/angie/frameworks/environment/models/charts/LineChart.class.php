<?php

/**
 * Implementation of line type of chart
 * 
 * @package angie.frameworks.envrionment
 * @subpackage models
 * @author Goran Blazin
 * 
 */

class LineChart extends Chart {
	
	/**
	 * Construct class for LineChart class
	 */
	function __construct($width, $height, $id = 'flot_chart_placeholder' ,$additional_tags = null) {
		parent::__construct($width, $height, $id, $additional_tags);
	} //__construct
	
	/**
	 * Renders the line chart. If $time_mode is true, x axis will be formated as date, where x values must be timestamps.
	 * If $mark_weekends is true, chart will mark all the weekend days with different colours.
	 * If $tool_tip chart will show a tool tip next to cursor when hovering the point
	 * 
	 * @param Boolean $time_mode
	 * @param Boolean $mark_weekends
	 * @param Boolean $tool_tip
	 * 
	 * Sets the render options
	 * @see Chart::render()
	 * @return string
	 */
	function render($date_mode = false, $mark_weekends = false, $tool_tip = false) {
		
		//add another serie for red zero value
		$zero_points = array();
		foreach($this->series as $serie) {
			foreach ($serie->getPoint() as $point) {
				if ($point->getY() == 0) {
					$zero_points[] = $point;
				} //if
			} //foreach
		} //foreach
		if (!empty($zero_points)) {
			$zero_serie = new ChartSerie($zero_points, array('points' => array('fillColor' => 'red'), 'lines' => array('show' => false), 'color' => '#E4E4E4'));
			$this->addSeries($zero_serie);
		} //if	
		
		$mode = ($date_mode) ? 'time' : null;
		$tick_size = ($date_mode) ? array(1,'day') : 1;
		
	  $this->setOption('xaxis', array(
      'mode' => $mode,
			'tickSize'    => $tick_size,
      'tickColor'   => '#E4E4E4',
    ));
    $this->setOption('yaxis', array(
			'show' => false,
     	'min'	 => 0
    ));
    $this->setOption('series', array(
			'lines'	=> array(
				'show' 		 	 => true,
     		'fill'			 => true,
				'lineWidth'  => 0.1,
				'shadowSize' => 0
		),
			'points' => array(
				'show' 			 => true,
				'fill'			 => true,
				'fillColor'  => 'black',
				'radius'		 => 2.5,
			),
		));
		$this->setOption('colors', array('#faf5e3'));
		
		$markings = ($mark_weekends) ? $this->chartNonWorkDaysMarkings() : null;
		
		$this->setOption('grid', array(
		  'markings' 			=> $markings,
			'borderWidth' 	=> 0,
			'hoverable'     => true
		));
		$return = parent::render();
		if ($tool_tip) {
			$return .= $this->toolTip();
		} //if
		return $return;
		
	} //render
	
	private function toolTip() {
		return 
		"<script type='text/javascript'>
		function showTooltip(x, y, contents) {
		        $('<div id=\"chart_tooltip\">' + contents + '</div>').css( {
		            position: 'absolute',
		            top: y - $('#".$this->placeholder_id."').offset().top + 5,
		            left: x - $('#".$this->placeholder_id."').offset().left + 5,
		            border: '1px solid #fdd',
		            padding: '2px',
		            'background-color': '#fee',
		            opacity: 0.80
		        }).appendTo(\"#".$this->placeholder_id."\").fadeIn(200);
		    }
		
		  var previousPoint = null;
      $('#". $this->placeholder_id ."').bind('plothover', function (event, pos, item) {
      
        if (item) {
          if (previousPoint != item.dataIndex) {
          	previousPoint = item.dataIndex
            $('#chart_tooltip').remove();
            var y = item.datapoint[1];
            var content = App.lang('No Activities');
            if (y === 1) {
            	content = App.lang('One Activity');
            } else if (y > 1) {
            	content = y + ' ' + App.lang('Activities');
            } //if
          	showTooltip(item.pageX, item.pageY, content);
          } //if
        } else {
          $('#chart_tooltip').remove();
          previousPoint = null;
        } //if
    });
		</script>";
	} //toolTip
	
} //LineChart