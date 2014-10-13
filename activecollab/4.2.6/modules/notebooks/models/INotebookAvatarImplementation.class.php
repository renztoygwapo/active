<?php

  /**
   * Notebook specific avatar implementation
   *
   * @package activeCollab.modules.notebooks
   * @subpackage models
   */
  class INotebookAvatarImplementation extends IAvatarImplementation {
    
    /**
     * Avatar sizes
     */
    const SIZE_PHOTO = 350;
    const SIZE_BIG = 145;
    const SIZE_MEDIUM = 32;
    const SIZE_SMALL = 16;
    
    /**
     * Avatar resize mode
     * 
     * @var string
     */
    public $resize_mode = self::RESIZE_MODE_FIT;
        
    /**
     * Construct notebook avatar implementation
     *
     * @param IAvatar $object
     */
    function __construct(IAvatar $object) {
      $this->avatars_folder = 'notebook_covers';
      $this->avatar_label_name = lang('Notebook Cover');
      
      $this->available_sizes = array(
        INotebookAvatarImplementation::SIZE_PHOTO => 'photo',
        INotebookAvatarImplementation::SIZE_BIG => 'large',
        INotebookAvatarImplementation::SIZE_MEDIUM => 'medium',
        INotebookAvatarImplementation::SIZE_SMALL => 'small'
      );

      if($object instanceof Notebook) {
        parent::__construct($object);
      } else {
        throw new InvalidInstanceError('object', $object, 'Notebook');
      } // if
    } // __construct

  }