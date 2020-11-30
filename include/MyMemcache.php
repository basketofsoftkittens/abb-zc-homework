<?php
namespace My;
class CacheFullException extends \Exception {}

class Memcache
{
	protected $cache = [];
	protected $filename;
	protected $size_limit;

	function __construct(?string $filename = null, ?int $size_limit = null)
	{
		if ($filename !== null)
		{
			$this->filename = $filename;
			if (file_exists($filename))
				$this->load();
		}
		if ($size_limit === null)
			$this->size_limit = PHP_INT_MAX;
		else
			$this->size_limit = $size_limit;
	}
	function __destruct()
	{
		if ($this->filename)
			$this->save();
	}

	/**
	 * Persist to disk
	 */
	public function save()
	{
		if ($this->filename)
			file_put_contents($this->filename, serialize($this->cache));
	}

	/**
	 * Load from disk
	 */
	public function load()
	{
		$this->cache = unserialize(file_get_contents($this->filename));
	}

	function get($key)
	{
		return $this->cache[$key]??null;
	}

	function set($key, $value)
	{
		if (count($this->cache) >= $this->size_limit)
			throw new CacheFullException("Cache has reached its size limit");
		return $this->cache[$key] = $value;
	}

	function unset($key)
	{
		unset($this->cache[$key]);
	}

	function size()
	{
		return count($this->cache);
	}
}
