<?php

  /**
   * Download implementation
   *
   * @package angie.framework.environment
   * @subpackage models
   */
  class IDownloadImplementation {
    
    /**
     * Parent object
     *
     * @var IDownload
     */
    protected $object;
    
    /**
     * Construct download helper
     *
     * @param IDownload $object
     */
    function __construct(IDownload $object) {
      $this->object = $object;
    } // __construct
    
    /**
     * Forward content to the browser
     * 
     * @param boolean $force
     * @param boolean $die
     */
    function send($force = true, $die = true) {
      download_file($this->getPath(), $this->object->getMimeType(), $this->object->getName(), $force, $die);
    } // send
    
    /**
     * Return full file path
     *
     * @return string
     */
    function getPath() {
      return UPLOAD_PATH . '/' . $this->object->getLocation();
    } // getPath
    
    /**
     * Returns true if this file is image
     *
     * @return boolean
     */
    function isImage() {
      return in_array($this->object->getMimeType(), array('image/jpg', 'image/jpeg', 'image/pjpeg', 'image/gif', 'image/png')) ||
        in_array(strtolower(get_file_extension($this->object->getName())), array('jpg', 'jpeg', 'png', 'gif'));
    } // isImage
    
    // ---------------------------------------------------
    //  Set content
    // ---------------------------------------------------
    
    /**
     * Set downloadble content from existing file on disk
     * 
     * If $save is set to true, save() method of parent object will be called. 
     * This function returns path of destination file on success
     *
     * @param string $path
     * @param boolean $save
     * @return string
     * @throws FileDnxError
     * @throws FileCopyError
     */
    function setContentFromFile($path, $save = true) {
      if(is_file($path) && is_readable($path)) {
        $destination_file = AngieApplication::getAvailableUploadsFileName();
        
        if(copy($path, $destination_file)) {
          $this->object->setName(basename($path));
          $this->object->setSize(filesize($path));
          $this->object->setLocation(basename($destination_file));
          $this->object->setMd5(md5_file($path));
					$this->object->setMimeType(get_mime_type($path, $this->object->getName()));
          
          if($save) {
            $this->object->save();
          } // if
          
          return $destination_file;
        } else {
          throw new FileCopyError($path, $destination_file);
        } // if
      } else {
        throw new FileDnxError($path);
      } // if
    } // setContentFromFile
    
    /**
     * Set content from uploaded file
     * 
     * If $save is set to true, save() method of parent object will be called. 
     * This function returns path of destination file on success
     *
     * @param array $file
     * @param boolean $save
     * @return string
     * @throws UploadError
     * @throws FileCopyError
     * @throws InvalidParamError
     */
    function setContentFromUploadedFile($file, $save = true) {
      if(is_array($file)) {
        if(isset($file['error']) && $file['error'] > 0) {
          throw new UploadError($file['error']);
        } // if
        
        $destination_file = AngieApplication::getAvailableUploadsFileName();
        if(move_uploaded_file($file['tmp_name'], $destination_file)) {
          $this->object->setName(array_var($file, 'name'));
          $this->object->setSize((integer) array_var($file, 'size'));
          $this->object->setMimeType(array_var($file, 'type'));
          $this->object->setLocation(basename($destination_file));
          $this->object->setMd5(md5_file($destination_file));
          
          if($save) {
            $this->object->save();
          } // if
          
          return $destination_file;
        } else {
          throw new FileCopyError($file['tmp_name'], $destination_file);
        } // if
      } else {
        throw new InvalidParamError('file', $file, '$file is not a valid uploaded file instance');
      } // if
    } // setContentFromUploadedFile
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------
    
    /**
     * Return download context URL
     *
     * @param boolean $force
     * @return string
     */
    function getDownloadUrl($force = null) {
      if($this->object instanceof IRoutingContext) {
        if($force === null) {
          $force = !$this->isImage();
        } // if

        $params = $this->object->getRoutingContextParams();

        if($force) {
          $params = array_merge((array) $params, array(
            'force' => true,
            'disposition' => 'attachment'
          ));
        } // if

        return Router::assemble($this->object->getRoutingContext() . '_download', $params);
      } else {
        return '#';
      } // if
    } // getDownloadUrl
    
    /**
     * Return thumbnail URL
     * 
     * @param integer $width
     * @param integer $height
     * @return string
     */
    function getThumbnailUrl($width = 80, $height = 80) {
      return $this->isImage() ? Thumbnails::getUrl($this->getPath(), $width, $height) : null;
    } // getThumbnailUrl
    
  }