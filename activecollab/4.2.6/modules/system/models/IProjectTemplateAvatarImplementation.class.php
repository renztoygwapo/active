<?php

/**
 * Template specific avatar implementation
 *
 * @package activeCollab.modules.notebooks
 * @subpackage models
 */
class IProjectTemplateAvatarImplementation extends IAvatarImplementation {
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
     * Construct template avatar implementation
     *
     * @param IAvatar $object
     */
    function __construct(IAvatar $object) {
        $this->avatars_folder = 'notebook_covers';
        $this->avatar_label_name = lang('Template Cover');

        $this->available_sizes = array(
          IProjectTemplateAvatarImplementation::SIZE_PHOTO => 'photo',
	        IProjectTemplateAvatarImplementation::SIZE_BIG => 'large',
	        IProjectTemplateAvatarImplementation::SIZE_MEDIUM => 'medium',
	        IProjectTemplateAvatarImplementation::SIZE_SMALL => 'small'
        );

        if ($object instanceof ProjectTemplate) {
            parent::__construct($object);
        } else {
            throw new InvalidInstanceError('object', $object, 'Template');
        } // if
    }

// __construct
}