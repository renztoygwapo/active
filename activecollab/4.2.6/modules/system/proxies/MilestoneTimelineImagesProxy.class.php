<?php

  /**
   * Proxy for generating background images for milestone timeline
   * 
   * @package activeCollab.modules.system
   * @subpackage proxies
   */
	class MilestoneTimelineImagesProxy extends ProxyRequestHandler {
		
		/**
		 * Width of one single day (needed for all renderings)
		 * 
		 * @var integer
		 */
		protected $day_width = 20;
		
		/**
		 * Prefix for cache images
		 * 
		 * @var string
		 */
		protected $image_prefix = 'milestone-timeline-';
		
		/**
		 * Workdays
		 * 
		 * @var array
		 */
		protected $workdays;
		
		/**
		 * Supports ttf
		 * 
		 * @var boolean
		 */
    protected $supports_ttf = false;
    
    /**
     * Supports ft
     * 
     * @var boolean
     */
    protected $supports_ft = false;
    
		/**
		 * Supports pfb
		 * 
		 * @var boolean
		 */
   	protected $supports_pfb = false;
		    
    /**
     * Return content type of the data that handler will forward to the browser
     * 
     * @return string
     */
    protected function getContentType() {
    	return 'image/png';
    } // getContentType
    
    /**
     * Handle proxy request
     */
    function execute() {
    	// retrieve day_width
    	$this->day_width = isset($_GET['day_width']) ? $_GET['day_width'] : 20;

    	// retrieve work days and clean them up
    	$this->workdays = isset($_GET['work_days']) ? $_GET['work_days'] : null;
    	if (!(is_array($this->workdays) && count($this->workdays) > 0)) {
    		$this->workdays = array(1,2,3,4,5);
    	} // if
    	sort($this->workdays);
    	
    	// support for text output
    	$this->supports_ttf = function_exists('imagettftext') && function_exists('imagettfbbox');
    	$this->supports_ft = function_exists('imagefttext') && function_exists('imageftbbox');
    	$this->supports_pfb = function_exists('imagepsloadfont');

    	// find which image we need to produce
    	$type = isset($_GET['type']) ? strtolower($_GET['type']) : null;
    	switch ($type) {
    		case 'days': $result = $this->getDaysBackgroundImage(); break;
    		case 'week_days' : $result = $this->getWeekDaysBackgroundImage(); break;
    		case 'month_days' : $result = $this->getMonthDaysBackgroundImage(); break;
    		default: die('not found'); break;
    	}
    	
    	if ($result) {
				header('Content-type: ' . $this->getContentType());
				echo $result;
    	} // if
    	die();
    } // execute
    
    /**
     * Renders the days background image
     */
    protected function getDaysBackgroundImage() {
    	$filename = WORK_PATH . '/' . $this->image_prefix . 'days-background-' . $this->day_width . '-' . implode('', $this->workdays) . '.png';
    	if (!is_file($filename)) {
    			$image_height = 1;    		
    			$image = imagecreatetruecolor(7 * $this->day_width, $image_height);

    			// fill it with workday background color
					$workday_background_color = imagecolorallocate($image, 255, 255, 255);
    			imagefill($image, 0, 0, $workday_background_color);
    			
    			// allocate other colors
    			$non_workday_background_color = imagecolorallocate($image, 250, 250, 250);
    			$border_color = imagecolorallocate($image, 241, 241, 241);

    			for ($workday = 0; $workday < 7; $workday++) {
    				// fill cells to reflect work and non work days
    				if (!in_array($workday, $this->workdays)) {
    					imagefilledrectangle($image, $workday * $this->day_width, 0, ($workday + 1) * $this->day_width, $image_height, $non_workday_background_color);
    				} // if
    				// draw a cell border
    				imageline($image, $workday * $this->day_width, 0 , $workday * $this->day_width , $image_height , $border_color);
    			} // for
    			
					imagepng($image, $filename);
					imagedestroy($image);
    	} // if
    	
    	if (is_file($filename)) {
    		return file_get_contents($filename);
    	} // if
    	
    	return null;
    } // getDaysBackgroundImage
    
    /**
     * Renders the weekdays background image
     */
    protected function getWeekDaysBackgroundImage() {
      $can_use_mbstring = extension_loaded('mbstring');

      if($can_use_mbstring) {
        mb_internal_encoding('UTF-8');
      } // if

			// @feature provide post script 1 alternative
    	$day_names = isset($_GET['day_names']) ? $_GET['day_names'] : array('Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat');
    	$first_letters = array();
    	
    	$filename = WORK_PATH . '/' . $this->image_prefix . 'week-days-background-' . $this->day_width . '-';
    	foreach ($day_names as $day_name) {
    		$letter = $can_use_mbstring ? mb_substr($day_name, 0, 1) : substr($day_name, 0, 1);
    		$filename .= $can_use_mbstring ? mb_strtolower($letter) : strtolower($letter);
    		$first_letters[] = $can_use_mbstring ? mb_strtoupper($letter) : strtoupper($letter);
    	} // foreach
    	$filename.= '.png';
    	
    	if (!is_file($filename)) {
   			$image_height = 30;    		
   			$image = imagecreatetruecolor(7 * $this->day_width, $image_height);
				$max_generated_height = 0;
				
    		// fill it with background color
				$background_color = imagecolorallocate($image, 255, 255, 255);
    		imagefill($image, 0, 0, $background_color);    			
				$text_color = imagecolorallocate($image, 204, 204, 204);
				

				if ($this->supports_ttf || $this->supports_ft) {
 					$font_size = 7;

					for ($weekday = 0; $weekday < 7; $weekday++) {
						if ($this->supports_ttf) {
							$dimensions = imagettfbbox($font_size, 0, $this->getFontFilePath(), $first_letters[$weekday]);	
						} else {
							$dimensions = imageftbbox($font_size, 0, $this->getFontFilePath(), $first_letters[$weekday]);
						} // if
						
						$height = $dimensions[3] - $dimensions[5];
						$max_generated_height = max($max_generated_height, $height);
						$offset = (($this->day_width - ($dimensions[4] - $dimensions[6])) / 2) + 1;
						
						if ($this->supports_ttf) {
							imagettftext($image, $font_size, 0, $weekday * $this->day_width + $offset, $height, $text_color, $this->getFontFilePath(), $first_letters[$weekday]);
						} else {
							imagefttext($image, $font_size, 0, $weekday * $this->day_width + $offset, $height, $text_color, $this->getFontFilePath(), $first_letters[$weekday]);
						} // if
					} // for

					// add bottom padding to the image
					$max_generated_height += 5;
					
				// GD native
				} else {
						$font_index = 2;
						for ($weekday = 0; $weekday < 7; $weekday++) {
							$max_generated_height = max($max_generated_height, imagefontheight($font_index));
							$offset = ($this->day_width - imagefontwidth($font_index) * $first_letters[$weekday]) / 2;
							imagestring($image, $font_index, ($this->day_width * $weekday) + $offset, 0, $first_letters[$weekday], $text_color);
						} // for
						$max_generated_height += 2;
				}
				
				$cropped_image = imagecreatetruecolor(7 * $this->day_width, $max_generated_height);
				imagecopy($cropped_image, $image, 0, 0, 0, 0, 31 * $this->day_width, $max_generated_height);					
				imagepng($cropped_image, $filename);
				imagedestroy($image);
				imagedestroy($cropped_image);				
    	} // if
    	
    	if (is_file($filename)) {
    		return file_get_contents($filename);
    	} // if
    	
    	return null;
    } // getWeekDaysBackgroundImage
    
    /**
     * Renders the month days background image
     */
    protected function getMonthDaysBackgroundImage() {
			// @feature provide post script 1 alternative
    	$filename = WORK_PATH . '/' . $this->image_prefix . 'month-days-background-' . $this->day_width . '.png';
    	   	
    	if (!is_file($filename)) {
    			$image_height = 30;    		
    			$image = imagecreatetruecolor(31 * $this->day_width, $image_height);
    			$max_generated_height = 0;
    			
    			// fill it with background color
					$background_color = imagecolorallocate($image, 255, 255, 255);
    			imagefill($image, 0, 0, $background_color);    			
					$text_color = imagecolorallocate($image, 138, 138, 138);
					
					// if use ttf or free type if supported
					if ($this->supports_ttf || $this->supports_ft) {
   					$font_size = 7;

						for ($monthday = 0; $monthday < 31; $monthday++) {
							if ($this->supports_ttf) {
								$dimensions = imagettfbbox($font_size, 0, $this->getFontFilePath(), $monthday + 1);	
							} else {
								$dimensions = imageftbbox($font_size, 0, $this->getFontFilePath(), $monthday + 1);
							} // if

							$height = $dimensions[3] - $dimensions[5];
							$max_generated_height = max($max_generated_height, $height);
							$offset = ($this->day_width - ($dimensions[4] - $dimensions[6])) / 2;
							
							if ($this->supports_ttf) {
								imagettftext($image, $font_size, 0, $monthday * $this->day_width + $offset, $height, $text_color, $this->getFontFilePath(), $monthday + 1);
							} else {
								imagefttext($image, $font_size, 0, $monthday * $this->day_width + $offset, $height, $text_color, $this->getFontFilePath(), $monthday + 1);
							} // if
						} // for

						// add bottom padding to the image
						$max_generated_height += 5;

					// GD native
					} else {
						$font_index = 2;
						for ($monthday = 0; $monthday < 31; $monthday++) {
							$max_generated_height = max($max_generated_height, imagefontheight($font_index));
							$offset = ($this->day_width - imagefontwidth($font_index) * strlen($monthday + 1)) / 2;
							imagestring($image, $font_index, ($this->day_width * $monthday) + $offset, 0, $monthday + 1, $text_color);
						} // for
						$max_generated_height += 2;
					} // if
					
					$cropped_image = imagecreatetruecolor(31 * $this->day_width, $max_generated_height);
					imagecopy($cropped_image, $image, 0, 0, 0, 0, 31 * $this->day_width, $max_generated_height);					
					imagepng($cropped_image, $filename);
					imagedestroy($image);
					imagedestroy($cropped_image);
    	} // if
    	
    	if (is_file($filename)) {
    		return file_get_contents($filename);
    	} // if
    	
    	return null;
    } // getMonthDaysBackgroundImage
    
    /**
     * Return font file path
     * 
     * @return string
     */
    protected function getFontFilePath() {
      if (!(defined('PROTECT_ASSETS_FOLDER') && PROTECT_ASSETS_FOLDER)) {
        return ASSETS_PATH . '/fonts/environment/default/dejavu/DejaVuSans.ttf';
      } else {
        return ANGIE_PATH . '/frameworks/environment/assets/default/fonts/dejavu/DejaVuSans.ttf';
      } // if
    } // getFontFilePath
    
  }