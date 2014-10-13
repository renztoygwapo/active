<?php

  /**
   * Forward thumbnail proxy
   * 
   * @package angie.frameworks.preview
   * @subpackage proxies
   */
  class FwForwardThumbnailProxy extends ProxyRequestHandler {
    
    /**
     * File name
     *
     * @var string
     */
    protected $name;
    
    /**
     * Image width (in px)
     *
     * @var integer
     */
    protected $width;
    
    /**
     * Image height (in px)
     *
     * @var integer
     */
    protected $height;
    
    /**
     * Image size (in bytes)
     *
     * @var integer
     */
    protected $size;
    
    /**
     * Construct proxy request handler
     * 
     * @param array $params
     */
    function __construct($params = null) {
      $this->name = isset($params['name']) && $params['name'] ? trim($params['name']) : null;
      $this->width = isset($params['width']) && $params['width'] ? (integer) $params['width'] : 0;
      $this->height = isset($params['height']) && $params['height'] ? (integer) $params['height'] : 0;
      $this->size = isset($params['ver']) && $params['ver'] ? (integer) $params['ver'] : 0;
      $this->crop = isset($params['crop']) && $params['crop'] ? $params['crop'] : false;
    } // __construct
    
    /**
     * Forward thumbnail
     */
    function execute() {
      if(empty($this->name) || empty($this->width) || empty($this->height)) {
        $this->notFound();
      } // if
      
      require_once ANGIE_PATH . '/functions/general.php';
      require_once ANGIE_PATH . '/functions/errors.php';
      require_once ANGIE_PATH . '/functions/web.php';
      
      $source = UPLOAD_PATH . '/' . $this->name;
      
      $thumb_file = ENVIRONMENT_PATH . "/thumbnails/{$this->name}-{$this->width}x{$this->height}";

      if ($this->crop) {
        $thumb_file .= '_crop';
      } // if
      
      if(is_file($source)) {
        if(filesize($source) == $this->size) {
          if(!is_file($thumb_file)) {
            if ($this->crop) {
              scale_and_crop_image($source, $thumb_file, $this->width);
            } else {
              scale_and_fit_image($source, $thumb_file, $this->width, $this->height, IMAGETYPE_JPEG);
            } // if
          } // if
        } else {
          $this->notFound();
        } // if
      } else {
        $this->imageNotFoundThumbnail($thumb_file);
      } // if
      
      if(is_file($thumb_file)) {
        download_file($thumb_file, 'image/jpeg', 'thumbnail.jpg', false, true);
      } else {
        $this->notFound();
      } // if
    } // execute
    
    /**
     * Create an empty image for situation when source is not found
     * 
     * @param string $thumb_file
     */
    protected function imageNotFoundThumbnail($thumb_file) {
      if(extension_loaded('gd')) {
        $image = imagecreatetruecolor($this->width, $this->height);
        
        $text_color = imagecolorallocate($image, 255, 255, 255);
        imagestring($image, 2, 5, 5, 'Not Found', $text_color);
        imagejpeg($image, $thumb_file, 80);
        
        imagedestroy($image);
      } // if
    } // imageNotFoundThumbnail
    
  }