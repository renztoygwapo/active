<?php

  /**
   * Avatar implementation that can be attached to any object
   *
   * @package angie.frameworks.avatar
   * @subpackage models
   */
  class IAvatarImplementation {
    
    // Size constants
    const SIZE_BIG = 32;
    const SIZE_SMALL = 16;
    
    // Resize mode
    const RESIZE_MODE_CROP = 'crop';
    const RESIZE_MODE_FIT = 'fit';

    /**
     * File type of avatar extensions
     *
     * @var string
     */
    const AVATAR_FILETYPE = IMAGETYPE_PNG;

    /**
     * Path to the location where uploaded avatars are and will be stored
     *
     * @var string
     */
    protected $avatars_path;

    /**
     * Url where avatars are located
     *
     * @var string
     */
    protected $avatars_url;

    /**
     * unique folder name for avatar type
     *
     * @var string
     */
    protected $avatars_folder;

    /**
     * Path to the location where default avatars are stored
     *
     * @var string
     */
    protected $available_sizes = false;

    /**
     * Parent object instance
     *
     * @var IAvatar
     */
    protected $object;

    /**
     * How will avatar be called
     *
     * @var string
     */
    protected $avatar_title = FALSE;
    
    /**
     * Avatar name
     * 
     * @var string
     */
    protected $avatar_label_name = FALSE;
    
    /**
     * Avatar resize mode
     * 
     * @var string
     */
    public $resize_mode = self::RESIZE_MODE_CROP;

    /**
     * Constructor for IAvatarImplementation
     *
     * @param IAvatar $object
     * @return IAvatarImplementation
     */
    function __construct(IAvatar $object) {
      if ($this->avatar_label_name === false) {
        $this->avatar_label_name = lang('Avatar');
      } // if

      if ($this->available_sizes == false) {
        $this->available_sizes = array(
          self::SIZE_BIG => 'large',
          self::SIZE_SMALL => 'small',
        );
      } // if

       $this->object = $object;

      $this->avatars_url = ROOT_URL . '/' . $this->avatars_folder;
      $this->avatars_path = ENVIRONMENT_PATH . '/' . PUBLIC_FOLDER_NAME . '/' . $this->avatars_folder;
    } // __construct
    
    /**
     * Return name of provided size
     * 
     * @param integer $size
     */
    function getSizeName($size) {
      return $this->available_sizes[$size];
    } // getSizeName
    
    /**
     * Return avatar label name
     * 
     * @return string
     */
    function getAvatarLabelName() {
      return $this->avatar_label_name;
    } // getAvatarLabelName
    
    /**
     * get the location where avatars will be uploaded
     * 
     * @return string
     */
    public function getAvatarsPath() {
      return $this->avatars_path;
    } // getAvatarsPath

    /**
     * Format size appendix
     *
     * @param integer $size
     * @return string
     */
    private function formatSize($size) {
      return $size . 'x' . $size;
    } // formatSize
    
    /**
     * Get Avatar extension
     * 
     * @return string
     */
    private function getAvatarExtension() {
      return image_type_to_extension(self::AVATAR_FILETYPE);
    } // getAvatarExtension

    /**
     * Assemble the filename
     *
     * @param integer $size
     * @return string
     */
    private function formatFileName($size) {
      return $this->object->getId() . '.' . $this->formatSize($size) . $this->getAvatarExtension();
    } // formatFileName

    /**
     * Check if avatar exists
     *
     * @param integer $size
     * @return boolean
     */
    public function avatarExists($size = 40) {
      return is_file($this->getPath($size));
    } // avatarExists

    /**
     * Check if other sizes and versions of this avatar exist
     *
     * @return string
     */
    public function repairPossible() {
      $recoverable_extensions = array('gif', 'png', 'jpg');

      foreach ($recoverable_extensions as $extension) {
        if (is_file($this->avatars_path . '/' . $this->object->getId() . '.original.' . $extension)) {
          return true;
        } // if

        foreach ($this->available_sizes as $size => $size_name) {
          if (is_file($this->avatars_path . '/' . $this->object->getId() . '.' . $size . 'x' . $size . '.' . $extension)) {
            return true;
          } // if
        } // foreach
      } // if

      return false;
    } // $this->repairPossible

    /**
     * Returns the url of the avatar
     *
     * @var integer $size
     * @return string
     */
    public function getUrl($size = 40) {
      if($this->avatarExists($size)) {
        $timestamp = method_exists($this->object, 'getUpdatedOn') && $this->object->getUpdatedOn() ? $this->object->getUpdatedOn()->getTimestamp() : filemtime($this->getPath($size));
        return $this->avatars_url . '/' . $this->formatFileName($size) . '?time=' . $timestamp;
      } else {
        if ($this->repairPossible()) {
          return AngieApplication::getProxyUrl('repair_avatar', SYSTEM_MODULE, array(
            'object_id' => $this->object->getId(),
            'size' => $size,
            'folder' => $this->avatars_folder,
            'available_sizes' => array_keys($this->available_sizes)
          ));
        } else {
          return $this->getDefaultUrl($size);
        } // if
      } // if
    } // getUrl

    /**
     * Returns the path to the current avatar at $size
     *
     * @param string $size
     * @return string
     */
    public function getPath($size) {
      return $this->avatars_path . '/' . $this->formatFileName($size);
    } // getPath
    
    /**
     * Returns the url of the originally uploaded image
     * 
     * @return string
     */
    public function getOriginalUrl() {
      return $this->avatars_url . '/' . $this->object->getId() . '.original' . $this->getAvatarExtension();       
    } // getOriginalUrl
    
    /**
     * Return path to the original uploaded image
     * 
     * @return string
     */
    public function getOriginalPath() {
      return $this->avatars_path . '/' . $this->object->getId() . '.original' . $this->getAvatarExtension();
    } // getOriginalPath

    /**
     * Get url of the default avatar
     *
     * @param integer $size
     * @return string
     */
    public function getDefaultUrl($size = 40) {
      return $this->avatars_url . '/default.' . $this->formatSize($size) . $this->getAvatarExtension();
    } // getDefaultUrl

    /**
     * Url to the page where avatar will be shown, with options to edit it
     *
     * @return string
     */
    public function getViewUrl() {
      if($this->object instanceof IRoutingContext) {
        return Router::assemble($this->object->getRoutingContext() . '_avatar_view', $this->object->getRoutingContextParams());
      } else {
        return null;
      } // if
    } // getViewUrl

    /**
     * Returns the biggest image we are using for current $object
     *
     * @return integer
     */
    public function biggestSize() {
      return max(array_keys($this->available_sizes));
    } // biggestSize
    
    /**
     * Describe parent object's avatar
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param boolean $for_interface
     * @param array $result
     */
    function describe(IUser $user, $detailed, $for_interface, &$result) {
      $result['avatar'] = array();
           
      foreach ($this->available_sizes as $size => $id) {
        $result['avatar'][$id] = $this->getUrl($size);
      } // foreach
      
      $result['avatar']['_full_size'] = $this->getOriginalUrl();
      $result['avatar']['_largest_size'] = $this->getUrl($this->biggestSize());
      
      $result['urls']['update_avatar'] = $this->getViewUrl();
    } // describe

    /**
     * Describe parent object's avatar
     *
     * @param IUser $user
     * @param boolean $detailed
     * @param array $result
     */
    function describeForApi(IUser $user, $detailed, &$result) {
      $result['avatar'] = array();

      foreach ($this->available_sizes as $size => $id) {
        $result['avatar'][$id] = $this->getUrl($size);
      } // foreach

      $result['avatar']['_full_size'] = $this->getOriginalUrl();
      $result['avatar']['_largest_size'] = $this->getUrl($this->biggestSize());

      if($detailed) {
        $result['urls']['update_avatar'] = $this->getViewUrl();
      } // if
    } // describeForApi
    
    // ---------------------------------------------------
    //  Operations
    // ---------------------------------------------------
    
    /**
     * Use provided file as object avatar
     * 
     * @param string $uploaded_file_path
     * @return boolean
     * @throws Error
     * @throws FileDnxError
     * @throws FileDeleteError
     */
    public function set($uploaded_file_path) {
      $destination_sizes = array_keys($this->available_sizes);
      krsort($destination_sizes);
      
      $destination_files = array();
      $failed_removal = array();
            
      if (!is_foreachable($destination_sizes)) {
        return false;
      } // if
      
      if (!is_file($uploaded_file_path)) {
        throw new FileDnxError($uploaded_file_path);
      } // if
      
      // check if previous avatars can be removed
      foreach ($destination_sizes as $size) {
        $file_path = $this->getPath($size);
        $destination_files[] = $file_path;
        if (is_file($file_path) && !file_is_writable($file_path)) {
          $failed_removal[] = $file_path;
        } // if
      } // foreach
      if (is_foreachable($failed_removal)) {
        throw new FileDeleteError($failed_removal);
      } // if

      // check size constraints
      check_image($uploaded_file_path);

      // open source image
      $image_resource = open_image($uploaded_file_path);
      
      // convert original image to AVATAR_TYPE
      $new_original_path = $this->getOriginalPath().'.temp';
      if (!convert_image($image_resource, $new_original_path, self::AVATAR_FILETYPE)) {
        throw new Error(lang('Could not convert :file to :type type', array(
          'file' => $uploaded_file_path,
          'type' => $this->getAvatarExtension(),
        )));
      } // if
            
      // first resize all avatars
      foreach ($destination_sizes as $size) {
        if ($this->resize_mode == self::RESIZE_MODE_CROP) {
          $resize_result = scale_and_crop_image($image_resource, $this->getPath($size).'.temp', $size, null, null, IMAGETYPE_PNG);
        } else if ($this->resize_mode == self::RESIZE_MODE_FIT) {
          $resize_result = scale_and_fit_image($image_resource, $this->getPath($size).'.temp', $size, $size, IMAGETYPE_PNG);
        } else {
          $resize_result = false;
        } // if
         
        if (!$resize_result) {
          throw new Error(lang('Could not resize :image to :sizex:size', array(
            'image' => $uploaded_file_path,
            'size' => $size
          )));
        } // if
      } // foreach
      
      // if all avatars are resized, it's time to overwrite previous avatars
      foreach ($destination_sizes as $size) {
        @rename($this->getPath($size).'.temp', $this->getPath($size));
      } // foreach
      @rename($new_original_path, $this->getOriginalPath());
      
      // close opened image      
      imagedestroy($image_resource['resource']);

      $this->updateParentObject();
      
      return true;
    } // useAvatar

    /**
     * Crops the existing avatar
     *
     * @param integer $left_offset - percentage of left offset
     * @param integer $top_offset - percentage of top offset
     * @return bool
     * @throws Error
     * @throws FileDeleteError
     * @throws Exception
     */
    public function crop($left_offset, $top_offset) {
      if ($this->resize_mode != self::RESIZE_MODE_CROP) {
        throw new Exception(lang(":label can't be cropped", array('label' => $this->getAvatarLabelName())));
      } // if
      
      $destination_sizes = array_keys($this->available_sizes);
      krsort($destination_sizes);
      
      $destination_files = array();
      $failed_removal = array();
            
      if (!is_foreachable($destination_sizes)) {
        return false;
      } // if
            
      // check if previous avatars can be removed
      foreach ($destination_sizes as $size) {
        $file_path = $this->getPath($size);
        $destination_files[] = $file_path;
        if (is_file($file_path) && !file_is_writable($file_path)) {
          $failed_removal[] = $file_path;
        } // if
      } // foreach
      if (is_foreachable($failed_removal)) {
        throw new FileDeleteError($failed_removal);
      } // if      

      // open source image
      $image_resource = open_image($this->getOriginalPath());
      
      $image_width = imagesx($image_resource['resource']);
      $image_height = imagesy($image_resource['resource']);
      
      $max_dimension = min(array($image_width, $image_height));
      
      $offset_x = floor($left_offset / 100 * ($image_width - $max_dimension));
      $offset_y = floor($top_offset / 100 * ($image_height - $max_dimension));
      
      if ($offset_x > $offset_y) {
        $offset_y = 0;
      } else {
        $offset_x = 0;
      } // if
      
      // first resize all avatars
      foreach ($destination_sizes as $size) {
        if (!scale_and_crop_image($image_resource, $this->getPath($size).'.temp', $size, $offset_x, $offset_y)) {
          throw new Error(lang('Could not resize :image to :sizex:size', array(
            'image' => $this->getOriginalPath(),
            'size' => $size
          )));
        } // if
      } // foreach
      
      // if all avatars are resized, it's time to overwrite previous avatars
      foreach ($destination_sizes as $size) {
        @rename($this->getPath($size).'.temp', $this->getPath($size));
      } // foreach
      
      // close opened image      
      imagedestroy($image_resource['resource']);

      $this->updateParentObject();

      return true;
    } // crop

    /**
     * Remove avatar and restore default one
     *
     * @return boolean
     * @throws FileDeleteError
     */
    public function remove() {
      $files = array();
      $not_deleteable = array();
      foreach ($this->available_sizes as $size => $label) {
        $current_file = $this->getPath($size);
        
        // collect list of files, and mark ones which are not deleteable
        if (file_exists($current_file)) {
          $files[] = $current_file;
          if (!is_writable($current_file)) {
            $not_deleteable[] = $current_file;
          } // if
        } // if        
      } // foreach
      
      if (is_foreachable($files)) {
        if (is_foreachable($not_deleteable)) {
          throw new FileDeleteError($not_deleteable);
        } // if
        
        foreach ($files as $file) {
          @unlink($file);
        } // foreach
        
        @unlink($this->getOriginalPath());
      } // if      
      
      return true;
    } // remove

    /**
     * After avatar has been updated, update parent object's information as well
     */
    private function updateParentObject() {
      if ($this->object instanceof DataObject && $this->object->fieldExists('updated_on')) {
        $this->object->setUpdatedOn(DateTimeValue::now());
        if (method_exists($this->object, 'setUpdatedBy')) {
          $this->object->setUpdatedBy(Authentication::getLoggedUser());
        } // if

        $this->object->save();
      } // if
    } // updateParentObject
    
    // ---------------------------------------------------
    //  URL-s
    // ---------------------------------------------------

    /**
     * Url to the page where new avatar can be uploaded
     *
     * @return string
     */
    public function getUploadUrl() {
      if($this->object instanceof IRoutingContext) {
        return Router::assemble($this->object->getRoutingContext() . '_avatar_upload', $this->object->getRoutingContextParams());
      } else {
        return null;
      } // if
    } // getUploadUrl

    /**
     * Url where avatar can be modified (resized/rescaled/etc...)
     *
     */
    public function getEditUrl() {
      if($this->object instanceof IRoutingContext) {
        return Router::assemble($this->object->getRoutingContext() . '_avatar_edit', $this->object->getRoutingContextParams());
      } else {
        return null;
      } // if
    } // getEditUrl

    /**
     * Url to the page where avatar will be removed
     * 
     * @return string
     */
    public function getRemoveUrl() {
      if($this->object instanceof IRoutingContext) {
        return Router::assemble($this->object->getRoutingContext() . '_avatar_remove', $this->object->getRoutingContextParams());
      } else {
        return null;
      } // if
    } // getRemoveUrl
    
    // ---------------------------------------------------
    //  Permissions
    // ---------------------------------------------------

    /**
     * Can $user upload avatar for this $object
     *
     * @param User $user
     * @return boolean
     */
    public function canUpload(User $user) {
      return $this->object->canEdit($user);
    } // canUpload

    /**
     * Can $user edit avatar for this $object
     *
     * @param User $user
     * @return boolean
     */
    public function canEdit(User $user) {
      return $this->object->canEdit($user);
    } // canEdit

    /**
     * Can $user remove avatar for this $object
     *
     * @param User $user
     * @return boolean
     */
    public function canRemove(User $user) {
      return $this->object->canEdit($user);
    } // canRemove
    
  }