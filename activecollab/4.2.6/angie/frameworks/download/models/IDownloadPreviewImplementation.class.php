<?php

  /**
   * Preview implementation for downloadble files
   *
   * @package angie.frameworks.preview
   * @subpackage models
   */
  class IDownloadPreviewImplementation extends IPreviewImplementation {
    
    /**
     * Parent object instance
     *
     * @var IDownload
     */
    protected $object;
    
    /**
     * Construct download preview implementation
     *
     * @param IPreview $object
     * @throws InvalidInstanceError
     */
    function __construct(IPreview $object) {
      if($object instanceof IPreview && $object instanceof IDownload) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, array('IPreview', 'IDownload'));
      } // if
    } // __construct
    
    /**
     * Returns true if parent object has preview
     *
     * @return boolean
     */
    function has() {
      return (boolean) $this->getPreviewType();
    } // has
    
    /**
     * Return small icon URL
     *
     * @return string
     */
    function getSmallIconUrl() {
      return $this->getIconUrl(16, 16);
    } // getSmallIconUrl
    
    /**
     * Return large icon URL
     *
     * @return string
     */
    function getLargeIconUrl() {
      return $this->getIconUrl(48, 48);
    } // getLargeIconUrl
    
    /**
     * Cached icon URL-s
     *
     * @var array
     */
    private $icon_urls = array();
    
    /**
     * Return icon URL
     *
     * @param integer $width
     * @param integer $height
     * @return string
     */
    private function getIconUrl($width, $height) {
      $dimensions = "{$width}x{$height}";
      
      if(!array_key_exists($dimensions, $this->icon_urls)) {
      	$this->icon_urls[$dimensions] = get_file_icon_url($this->object->getName(), $dimensions);
      } // if
      
      return $this->icon_urls[$dimensions];
    } // getIconUrl

    /**
     * Get Thumbnail url
     */
    function getThumbnailUrl() {
      if ($this->getPreviewType() == DOWNLOAD_PREVIEW_IMAGE) {
        return Thumbnails::getUrl($this->object->download()->getPath(), 80, 80);
      } else {
        return $this->getLargeIconUrl();
      } // if
    } // getThumbnailUrl
    
    /**
     * Render small preview
     *
     * @return string
     */
    function renderSmall() {
      return $this->renderPreview(80, 80);
    } // renderSmall
    
    /**
     * Render large preview
     *
     * @return string
     */
    function renderLarge() {
      return $this->renderPreview(550, 300);
    } // renderLarge
    
    /**
     * Cached rendered previews
     *
     * @var array
     */
    private $previews = array();
    
    /**
     * Render preview
     *
     * @param integer $width
     * @param integer $height
     * @param boolean $hires
     * @return string
     */
    function renderPreview($width, $height, $hires = false) {
      $dimensions = "{$width}x{$height}";
      
      if(!array_key_exists($dimensions, $this->previews)) {
        switch($this->getPreviewType()) {
          
          // Render video player
          case DOWNLOAD_PREVIEW_VIDEO:
            if($width > 80) {
              $id = HTML::uniqueId('video_player');

              $this->previews[$dimensions] = '<div class="jwplayer_file_preview_wrapper"><div id="' . $id . '" class="file_preview"></div></div><script type="text/javascript">jwplayer("' . $id . '").setup(' . JSON::encode(array(
                'flashplayer' => AngieApplication::getAssetUrl('jwplayer/player.swf', ENVIRONMENT_FRAMEWORK, 'flash'),
                'file' => $this->object->download()->getDownloadUrl(false),
                'provider' => 'video',
                'width' => 640,
                'height' => 360,
              )) . ')</script>';
            } // if

            break;
            
          // Render audio player
          case DOWNLOAD_PREVIEW_AUDIO:
            $id = HTML::uniqueId('video_player');

            $this->previews[$dimensions] = '<div class="jwplayer_file_preview_wrapper audio"><div id="' . $id . '" class="file_preview"></div></div><script type="text/javascript">jwplayer("' . $id . '").setup(' . JSON::encode(array(
              'flashplayer' => AngieApplication::getAssetUrl('jwplayer/player.swf', ENVIRONMENT_FRAMEWORK, 'flash'),
              'file' => $this->object->download()->getDownloadUrl(false),
              'provider' => 'audio',
              'width' => 640,
              'height' => 24,
              'controlbar' => 'bottom',
            )) . ')</script>';

            break;
            
          // Render image preview
          case DOWNLOAD_PREVIEW_IMAGE:
            $this->previews[$dimensions] = '<div class="file_preview">' . HTML::openTag('a', array(
              'href'    => $this->object->download()->getDownloadUrl(true),
              'title'   => lang('Click to Download'),
              'target'  => '_blank',
            )) . HTML::openTag('img', array(
              'src'     => Thumbnails::getUrl($this->object->download()->getPath(), $width, $height),
              'style'   => "max-width: {$width}px; max-height: {$height}px"
            )) . '</a></div>';
            
            break;
            
          // Render flash preview box
          case DOWNLOAD_PREVIEW_FLASH:
            break;
            
          // Render icon inside of preview box
          default:
            $this->previews[$dimensions] = HTML::openTag('div', array(
              'class' => 'preview', 
              'width' => $width, 
              'height' => $height, 
            )) . HTML::openTag('a', array(
              'href' => $this->object->download()->getDownloadUrl(true),
              'title' => lang('Click to Download'),
              'target' => '_blank',
            )) . HTML::openTag('img', array(
              'src' => $this->getIconUrl(48, 48)
            )) . '</a></div>';

        } // switch
      } // if
      
      return $this->previews[$dimensions];
    } // renderPreview
    
    /**
     * Cached preview type
     *
     * @var mixed
     */
    private $preview_type = null;
    
    /**
     * Return preview type
     *
     * @return mixed
     */
    function getPreviewType() {
      if($this->preview_type === null) {
        $this->preview_type = Attachments::getPreviewType($this->object);
      } // if

      return $this->preview_type;
    } // getPreviewType

    /**
     * Get preview url
     *
     * @return String
     */
    function getPreviewUrl() {
      $route_name = $this->object->getRoutingContext() . '_preview';
      try {
        return Router::assemble($route_name, array_merge($this->object->getRoutingContextParams(), array('attachment_id' => $this->object->getId())));
      } catch (Exception $e) {
        return $this->getThumbnailUrl();
      } // try
    } // getPreviewUrl

    /**
     * Describe preview information
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     * @return array
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      parent::describe($user, $detailed, $for_interface, $result);

      // if we have preview for this item describe it
      if ($this->has()) {
        $result['urls']['preview'] = $this->getPreviewUrl();
      } // if
    } // describe
    
  }