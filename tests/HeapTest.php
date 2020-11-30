<?php
use PHPUnit\Framework\TestCase;

final class HeapTest extends TestCase
{
	public function testMaxHeap()
	{
		$h = new MaxHeap([7,3,2,9,12]);
		$this->assertEquals(12, $h->pop());
		$this->assertEquals(9, $h->pop());
		$this->assertEquals(7, $h->pop());
		$this->assertEquals(3, $h->pop());
		$this->assertEquals(2, $h->pop());
		$this->assertEquals(null, $h->pop());
	}

	public function testMinHeap()
	{
		$h = new MinHeap([5,0,3]);
		$this->assertEquals(0, $h->pop());
		$this->assertEquals(3, $h->pop());
		$this->assertEquals(5, $h->pop());
		$this->assertEquals(null, $h->pop());
	}

	public function testInsert()
	{
		$h = new MinHeap([5,0,3]);
		$h->insert(-100);
		$h->insert(-101);
		$this->assertEquals(-101, $h->pop());
		$this->assertEquals(-100, $h->pop());
	}
}