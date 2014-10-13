<?php

  /**
   * Anonymous User specific avatar implementation
   *
   * @package angie.frameworks.authentication
   * @subpackage models
   */
  class IAnonymousUserAvatarImplementation extends IAvatarImplementation {
    
    // Avatar sizes
    const SIZE_PHOTO = 256;
    const SIZE_BIG = 40;
    const SIZE_SMALL = 16;
        
    /**
     * Construct anonymous user avatar implementation
     *
     * @param IAvatar $object
     * @throws InvalidInstanceError
     */
    function __construct(IAvatar $object) {
      $this->avatars_folder = 'avatars';
      $this->avatar_label_name = lang('Avatar');
      
      $this->available_sizes = array(
        IAnonymousUserAvatarImplementation::SIZE_PHOTO => 'photo',
        IAnonymousUserAvatarImplementation::SIZE_BIG => 'large',
        IAnonymousUserAvatarImplementation::SIZE_SMALL => 'small'
      );

      if($object instanceof AnonymousUser) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Anonymous');
      } // if
    } // __construct
    
    /**
     * Get avatar url
     *
     * @param integer $size
     * @return string
     */
    function getUrl($size = IAnonymousUserAvatarImplementation::SIZE_BIG) {
      return AngieApplication::getImageUrl("user-roles/member.{$size}x{$size}.png", AUTHENTICATION_FRAMEWORK, AngieApplication::INTERFACE_DEFAULT);
    } // getUrl

  }