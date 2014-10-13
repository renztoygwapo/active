<?php

  /**
   * User specific avatar implementation
   *
   * @package activeCollab.modules.system
   * @subpackage models
   */
  class IUserAvatarImplementation extends IAvatarImplementation {
    
    // Avatar sizes
    const SIZE_PHOTO = 256;
    const SIZE_BIG = 40;
    const SIZE_SMALL = 16;
        
    /**
     * Construct user avatar implementation
     *
     * @param IAvatar $object
     * @throws InvalidInstanceError
     */
    function __construct(IAvatar $object) {
      $this->avatars_folder = 'avatars';
      $this->avatar_label_name = lang('Avatar');
      
      $this->available_sizes = array(
        IUserAvatarImplementation::SIZE_PHOTO => 'photo',
        IUserAvatarImplementation::SIZE_BIG => 'large',
        IUserAvatarImplementation::SIZE_SMALL => 'small'
      );

      if($object instanceof IUser) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'IUser');
      } // if
    } // __construct

    /**
     * Get url of the default avatar
     *
     * @param integer $size
     * @return string
     */
    public function getDefaultUrl($size = 40) {
      return AngieApplication::getImageUrl("user-roles/member.{$size}x{$size}.png", AUTHENTICATION_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT);
    } // getDefaultUrl

  }