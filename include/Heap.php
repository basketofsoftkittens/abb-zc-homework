<?php
class MaxHeap
{
	protected $data = [];

	protected function compare($x, $y)
	{
		return $x<$y;
	}

	protected function swap(int $index1, int $index2)
	{
		$temp = $this->data[$index1];
		$this->data[$index1] = $this->data[$index2];
		$this->data[$index2] = $temp;
	}

	function __construct(array $init = [])
	{
		$this->data = $init;
		$this->build_heap();
	}

	/**
	 * Build a heap from an array O(n)
	 */
	protected function build_heap()
	{
		$start_index = (count($this->data) / 2) - 1;
	   for ($i = $start_index; $i>=0; --$i)
      $this->heapify($i);
	}

	protected function heapify(int $index)
	{
		// echo "Heapify index ", $index, "({$this->data[$index]}): ", join(",", $this->data), PHP_EOL;
		$left = $index * 2 + 1;
		$right = $index * 2 + 2;
		$max = $index;
		if ($left < count($this->data) and $this->compare($this->data[$max], $this->data[$left]))
			$max = $left;
		if ($right < count($this->data) and $this->compare($this->data[$max], $this->data[$right]))
			$max = $right;
		if ($max != $index)
		{
			$this->swap($index, $max);
			// echo "\tresult: ", join(",", $this->data), PHP_EOL;
			$this->heapify($max);
		}
	}

	protected function heapify_bottom_up(int $index)
	{
		$parent = ($index - 1) / 2;
		if ($this->compare($this->data[$parent], $this->data[$index]))
		{
			$this->swap($index, $parent);
			$this->heapify_bottom_up($parent);
		}
	}

	public function pop()
	{
		if (empty($this->data))
			return null;
		$max = $this->data[0];
		$last = array_pop($this->data);
		if (!empty($this->data))
		{
			$this->data[0] = $last;
			$this->heapify(0);
		}
		return $max;
	}

	public function insert($value)
	{
		$this->data[] = $value;
		$this->heapify_bottom_up(count($this->data) - 1);
	}
}
class MinHeap extends MaxHeap
{
	protected function compare($x, $y)
	{
		return $x>$y;
	}
}
