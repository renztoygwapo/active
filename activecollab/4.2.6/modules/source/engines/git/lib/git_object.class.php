<?php

class GitObject
{
    /**
     * @brief (Git) The repository this object belongs to.
     */
    public $repo;
    public $type;
    public $name = NULL;

    /**
     * @brief Get the object's cached SHA-1 hash value.
     *
     * @returns (string) The hash value (binary sha1).
     */
    public function getName() {	return $this->name; }

    /**
     * @brief Get the object's type.
     *
     * @returns (integer) One of Git::OBJ_COMMIT, Git::OBJ_TREE or
     * GIT::OBJ_BLOB.
     */
    public function getType() { return $this->type; }

    /**
     * @brief Create a GitObject of the specified type.
     *
     * @param $repo (Git) The repository the object belongs to.
     * @param $type (integer) Object type (one of Git::OBJ_COMMIT,
     * Git::OBJ_TREE, Git::OBJ_BLOB).
     * @returns A new GitCommit, GitTree or GitBlob object respectively.
     */
    static public function create($repo, $type)
    {
	if ($type == Git::OBJ_COMMIT)
	    return new GitLibCommit($repo);
	if ($type == Git::OBJ_TREE)
	    return new GitLibTree($repo);
	if ($type == Git::OBJ_BLOB)
	    return new GitLibBlob($repo);
	throw new Exception(sprintf('unhandled object type %d', $type));
    }

    /**
     * @brief Internal function to calculate the hash value of a git object of the
     * current type with content $data.
     *
     * @param $data (string) The data to hash.
     * @returns (string) The hash value (binary sha1).
     */
    protected function hash($data)
    {
	$hash = hash_init('sha1');
	hash_update($hash, Git::getTypeName($this->type));
	hash_update($hash, ' ');
	hash_update($hash, strlen($data));
	hash_update($hash, "\0");
	hash_update($hash, $data);
	return hash_final($hash, TRUE);
    }

    /**
     * @brief Internal constructor for use from derived classes.
     *
     * Never use this function except from a derived class. Use the
     * constructor of a derived class, create() or Git::getObject() instead.
     */
    public function __construct($repo, $type)
    {
	$this->repo = $repo;
	$this->type = $type;
    }

    /**
     * @brief Populate this object with values from its string representation.
     *
     * Note that the types of $this and the serialized object in $data have to
     * match.
     *
     * @param $data (string) The serialized representation of an object, as
     * it would be stored by git.
     */
    public function unserialize($data)
    {
	$this->name = $this->hash($data);
	$this->_unserialize($data);
    }

    /**
     * @brief Get the string representation of an object.
     *
     * @returns The serialized representation of the object, as it would be
     * stored by git.
     */
    public function serialize()
    {
	return $this->_serialize();
    }

    /**
     * @brief Update the SHA-1 name of an object.
     *
     * You need to call this function after making changes to attributes in
     * order to have getName() return the correct hash.
     */
    public function rehash()
    {
	$this->name = $this->hash($this->serialize());
    }

    /**
     * @brief Write this object in its serialized form to the git repository
     * given at creation time.
     */
    public function write()
    {
	$sha1 = sha1_hex($this->name);
	$path = sprintf('%s/objects/%s/%s', $this->repo->dir, substr($sha1, 0, 2), substr($sha1, 2));
	if (file_exists($path))
	    return FALSE;
	$dir = dirname($path);
	if (!is_dir($dir))
	    mkdir(dirname($path), 0770);
	$f = fopen($path, 'ab');
	flock($f, LOCK_EX);
	ftruncate($f, 0);
	$data = $this->serialize();
	$data = Git::getTypeName($this->type).' '.strlen($data)."\0".$data;
	fwrite($f, gzcompress($data));
	fclose($f);
	return TRUE;
    }
}
?>