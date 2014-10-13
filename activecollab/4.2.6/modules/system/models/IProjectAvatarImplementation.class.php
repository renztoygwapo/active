<?php

  /**
   * Project specific avatar implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IProjectAvatarImplementation extends IAvatarImplementation {
    
    /**
     * Avatar sizes
     */
    const SIZE_PHOTO = 256;
    const SIZE_LARGE = 80;
    const SIZE_BIG = 40;
    const SIZE_SMALL = 16;
    
    /**
     * Avatar resize mode
     * 
     * @var string
     */
    public $resize_mode = self::RESIZE_MODE_FIT;
        
    /**
     * Construct project avatar implementation
     *
     * @param IAvatar $object
     */
    function __construct(IAvatar $object) {
      $this->avatars_folder = 'projects_icons';
      $this->avatar_label_name = lang('Project icon');
      
      $this->available_sizes = array(
        IProjectAvatarImplementation::SIZE_PHOTO => 'photo',
        IProjectAvatarImplementation::SIZE_LARGE => 'large',
        IProjectAvatarImplementation::SIZE_BIG => 'big',
        IProjectAvatarImplementation::SIZE_SMALL => 'small'
      );

      if($object instanceof Project) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Project');
      } // if
    } // __construct

  }