<?php

  /**
   * Company specific avatar implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class ICompanyAvatarImplementation extends IAvatarImplementation {
    
    /**
     * Avatar sizes
     */
    const SIZE_PHOTO = 256;
    const SIZE_LARGE = 80;
    const SIZE_MEDIUM = 40;
    const SIZE_SMALL = 16;
    
    /**
     * Avatar resize mode
     * 
     * @var string
     */
    public $resize_mode = self::RESIZE_MODE_FIT;
        
    /**
     * Construct user avatar implementation
     *
     * @param IAvatar $object
     */
    function __construct(IAvatar $object) {
      $this->avatars_folder = 'logos';
      $this->avatar_label_name = lang('Company Logo');
      
      $this->available_sizes = array(
        ICompanyAvatarImplementation::SIZE_PHOTO => 'photo',
        ICompanyAvatarImplementation::SIZE_LARGE => 'large',
        ICompanyAvatarImplementation::SIZE_MEDIUM => 'medium',
        ICompanyAvatarImplementation::SIZE_SMALL => 'small'
      );

      if($object instanceof Company) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Company');
      } // if
    } // __construct

  }