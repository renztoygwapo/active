<?php

require_once('git_object.class.php');
require_once('git_commit_stamp.class.php');

class GitLibCommit extends GitObject
{
    /**
     * @brief (string) The tree referenced by this commit, as binary sha1
     * string.
     */
    public $tree;

    /**
     * @brief (array of string) Parent commits of this commit, as binary sha1
     * strings.
     */
    public $parents;

    /**
     * @brief (GitCommitStamp) The author of this commit.
     */
    public $author;

    /**
     * @brief (GitCommitStamp) The committer of this commit.
     */
    public $committer;

    /**
     * @brief (string) Commit summary, i.e. the first line of the commit message.
     */
    public $summary;

    /**
     * @brief (string) Everything after the first line of the commit message.
     */
    public $detail;

    public function __construct($repo)
    {
	parent::__construct($repo, Git::OBJ_COMMIT);
    }

    public function _unserialize($data)
    {
	$lines = explode("\n", $data);
	unset($data);
	$meta = array('parent' => array());
	while (($line = array_shift($lines)) != '')
	{
	    $parts = explode(' ', $line, 2);
	    if (!isset($meta[$parts[0]]))
		$meta[$parts[0]] = array($parts[1]);
	    else
		$meta[$parts[0]][] = $parts[1];
	}

	$this->tree = sha1_bin($meta['tree'][0]);
	$this->parents = array_map('sha1_bin', $meta['parent']);
	$this->author = new GitCommitStamp;
	$this->author->unserialize($meta['author'][0]);
	$this->committer = new GitCommitStamp;
	$this->committer->unserialize($meta['committer'][0]);

	$this->summary = array_shift($lines);
	$this->detail = implode("\n", $lines);

        $this->history = NULL;
    }

    public function _serialize()
    {
	$s = '';
	$s .= sprintf("tree %s\n", sha1_hex($this->tree));
	foreach ($this->parents as $parent)
	    $s .= sprintf("parent %s\n", sha1_hex($parent));
	$s .= sprintf("author %s\n", $this->author->serialize());
	$s .= sprintf("committer %s\n", $this->committer->serialize());
	$s .= "\n".$this->summary."\n".$this->detail;
	return $s;
    }

    /**
     * @brief Get commit history in topological order.
     *
     * @returns (array of GitCommit)
     */
    public function getHistory(Git $git_object)
    {
        if ($this->history)
            return $this->history;

        /* count incoming edges */
        $inc = array();

        $queue = array($this);
        $i = 1;
        while (($commit = array_shift($queue)) !== NULL)
        {
            foreach ($commit->parents as $parent)
            {
                $object = $git_object->getObject($parent);
                
                if (!isset($inc[sha1_hex($parent)]))
                {
                    $inc[sha1_hex($parent)] = $i;
                    $i++;
                    $queue[] = $this->repo->getObject($parent);
                }
            }
        }
        $inc = array_reverse(array_flip($inc));
        array_push($inc, sha1_hex($this->name));
        return ($this->history = $inc);
    }

    /**
     * @brief Get the tree referenced by this commit.
     *
     * @returns The GitTree referenced by this commit.
     */
    public function getTree()
    {
        return $this->repo->getObject($this->tree);
    }

    /**
     * @copybrief GitTree::find()
     *
     * This is a convenience function calling GitTree::find() on the commit's
     * tree.
     *
     * @copydetails GitTree::find()
     */
    public function find($path)
    {
        return $this->getTree()->find($path);
    }

    static public function treeDiff($a, $b)
    {
        return GitTree::treeDiff($a ? $a->getTree() : NULL, $b ? $b->getTree() : NULL);
    }
}
?>