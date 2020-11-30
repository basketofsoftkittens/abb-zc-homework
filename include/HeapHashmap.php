<?php
/**
 * Keeps a hashmap that keeps index of
 * each key in the heap, so that elements
 * can be accessed by key within the heap too.
 */
class HeapHashmap extends MinHeap
{
	function compare($x, $y)
	{
		return $x->value > $y->value;
	}
	protected $key_heap_index = [];

	function key_exists($key)
	{
		return isset($this->key_heap_index[$key]);
	}

	function key_index($key)
	{
		return $this->key_heap_index[$key];
	}

	function get($key)
	{
		return $this->data[$this->key_index($key)];
	}

	function size()
	{
		return count($this->data);
	}

	protected function swap(int $index1, int $index2)
	{
		if ($index1 == $index2) return;

		$this->key_heap_index[$this->data[$index2]->key] = $index1;
		$this->key_heap_index[$this->data[$index1]->key] = $index2;
		parent::swap($index1, $index2);
	}

	function insert($value)
	{
		throw new Exception("Should not be called directly.");
	}

	function insert_key_value($key, $value)
	{
		assert(!isset($this->key_heap_index[$key]));
		$this->key_heap_index[$key] = count($this->data);
		return parent::insert((object)["key"=>$key, "value"=>$value]);
	}

	function pop()
	{
		$popped_key = $this->data[0]->key;
		unset($this->key_heap_index[$popped_key]);

		$last_key = end($this->data)->key;
		$result = parent::pop();

		if (!empty($this->data))
			$this->key_heap_index[$last_key] = 0; // Bringing last element first in the heap

		return $result;
	}

	/**
	 * Update a specified key with a new value
	 */
	function update($key, $new_value)
	{
		assert(isset($this->key_heap_index[$key]));
		$heap_index = $this->key_heap_index[$key];

		$old_value = $this->data[$heap_index]->value;
		$this->data[$heap_index]->value = $new_value;
		if ($new_value > $old_value) // Needs to sift down the heap
			$this->heapify($heap_index);
		elseif ($new_value < $old_value) // Needs to sift up the heap
			$this->heapify_bottom_up($heap_index);
	}

}