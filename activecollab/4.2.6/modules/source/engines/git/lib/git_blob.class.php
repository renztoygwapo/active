<?php

require_once('git_object.class.php');

class GitLibBlob extends GitObject
{
    /**
     * @brief The data contained in this blob.
     */
    public $data = NULL;

    public function __construct($repo)
    {
	parent::__construct($repo, Git::OBJ_BLOB);
    }

    public function _unserialize($data)
    {
	$this->data = $data;
    }

    public function _serialize()
    {
	return $this->data;
    }
}
?>