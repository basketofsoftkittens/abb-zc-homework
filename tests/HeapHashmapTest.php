<?php
use PHPUnit\Framework\TestCase;

final class HeapHashmapTest extends TestCase
{
	public function testHeapHashmap()
	{
		$h = new HeapHashmap();
		$h->insert_key_value("key2", 2);
		$h->insert_key_value("key0", 0);
		$h->insert_key_value("key1", 1);

		$obj=$h->pop();
		$this->assertEquals(0, $obj->value);
		$this->assertEquals("key0", $obj->key);

		$obj=$h->pop();
		$this->assertEquals(1, $obj->value);
		$this->assertEquals("key1", $obj->key);

		$obj=$h->pop();
		$this->assertEquals(2, $obj->value);
		$this->assertEquals("key2", $obj->key);

	}

	/**/
	public function testStressHeapHashmap()
	{
		$h = new HeapHashmap();
		$arr = range(0,100);
		shuffle($arr);
		foreach ($arr as $i)
			$h->insert_key_value("key{$i}", $i);

		for ($i=0;$i<100;++$i)
		{
			$obj = $h->pop();
			$this->assertEquals($i, $obj->value);
			$this->assertEquals("key{$i}", $obj->key);
		}
	}
	/**/

	public function testUpdateSiftDown()
	{
		$h = new HeapHashmap();
		$h->insert_key_value("key0", 0);
		$h->insert_key_value("key1", 1);
		$h->insert_key_value("key2", 2);

		$h->update("key1", 100);

		$obj=$h->pop();
		$this->assertEquals(0, $obj->value);
		$this->assertEquals("key0", $obj->key);

		$obj=$h->pop();
		$this->assertEquals(2, $obj->value);
		$this->assertEquals("key2", $obj->key);

		$obj=$h->pop();
		$this->assertEquals(100, $obj->value);
		$this->assertEquals("key1", $obj->key);
	}

	public function testUpdateSiftUp()
	{
		$h = new HeapHashmap();
		$h->insert_key_value("key0", 0);
		$h->insert_key_value("key1", 1);
		$h->insert_key_value("key2", 2);

		$h->update("key1", -100);

		$obj=$h->pop();
		$this->assertEquals(-100, $obj->value);
		$this->assertEquals("key1", $obj->key);

		$obj=$h->pop();
		$this->assertEquals(0, $obj->value);
		$this->assertEquals("key0", $obj->key);

		$obj=$h->pop();
		$this->assertEquals(2, $obj->value);
		$this->assertEquals("key2", $obj->key);

	}
}