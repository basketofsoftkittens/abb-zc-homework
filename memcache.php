<?php
class MyMemcache
{
	protected $cache = [];
	protected string $filename;

	function __construct(?string $filename = null)
	{
		if ($filename !== null)
		{
			$this->filename = $filename;
			if (file_exists($filename))
				$this->load();
		}
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
		return $this->cache[$key] = $value;
	}

	function unset($key)
	{
		unset($this->cache[$key]);
	}

}
