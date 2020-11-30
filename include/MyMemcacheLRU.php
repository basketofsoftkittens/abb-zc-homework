<?php
namespace My;

class MemcacheLRU extends Memcache
{
	function __construct(...$args)
	{
		parent::__construct(...$args);
		$this->heap = new \HeapHashmap();
	}

	function time()
	{
		return time();
	}
	/**
	 * Evicts the least recently used element
	 */
	protected function evict()
	{
		$key = $this->heap->pop()->key;
		// echo "Evicting {$key}\n";
		unset($this->cache[$key]);
	}

	function get($key)
	{
		if ($this->heap->key_exists($key))
		{
			$this->heap->update($key, $this->time());
			return $this->cache[$key];
		}
		return null;
	}

	function set($key, $value)
	{
		// If already exists, refresh time and update value
		if ($this->heap->key_exists($key))
		{
			$this->heap->update($key, $this->time());
		}
		else // New entry into the cache
		{
			if ($this->size() >= $this->size_limit) // Cache full, evict
				$this->evict();

			$this->heap->insert_key_value($key, $this->time());
		}
		return $this->cache[$key] = $value;
	}

	function unset($key)
	{
		$this->heap->update($key, -1);
		$this->evict();
	}

	function set_size_limit(int $new_size_limit)
	{
		assert($new_size_limit>=1);
		$this->size_limit = $new_size_limit;
		while ($this->size() > $this->size_limit)
			$this->evict();
	}

}