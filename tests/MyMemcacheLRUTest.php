<?php
namespace My;
use PHPUnit\Framework\TestCase;

function time()
{
	static $i=1;
	return $i++;
}
final class MyMemcacheLRUTest extends TestCase
{
	function setUp(): void
	{
		$this->cache = new MemcacheLRU();
	}

	public function testSetAndGet()
	{
		$this->cache->set("test_key", "test_value");

		$this->assertEquals($this->cache->get("test_key"), "test_value");

		$this->assertNull($this->cache->get("non-existent-key"));
	}

	public function testUnset()
	{
		$this->cache->set("test_key", "test_value");
		$this->assertEquals($this->cache->get("test_key"), "test_value");
		$this->assertEquals($this->cache->size(), 1);

		$this->cache->unset("test_key");
		$this->assertNull($this->cache->get("test_key"));
		$this->assertEquals($this->cache->size(), 0);
	}

	public function testOverflow()
	{
		$this->cache->set_size_limit(2);
		$this->cache->set("key1", 1);
		$this->cache->set("key2", 2);

		$this->cache->set("key3", 3); // Should evict key1

		$this->assertNull($this->cache->get("key1"));
	}

	public function testSetSizeLimit()
	{
		$this->cache->set("key1", 1);
		$this->cache->set("key2", 2);
		$this->cache->set("key3", 3);

		$this->cache->set("key4", 4);
		$this->cache->set("key5", 5);

		$this->cache->set_size_limit(2); // Shoulde evict 1 and 2

		$this->assertNull($this->cache->get("key1"));
		$this->assertNull($this->cache->get("key2"));
		$this->assertNull($this->cache->get("key3"));
		$this->assertEquals($this->cache->get("key4"), 4);
		$this->assertEquals($this->cache->get("key5"), 5);
	}

	public function testLRU()
	{
		$this->cache->set_size_limit(3);

		$this->cache->set("key1", 1);
		$this->cache->set("key2", 2);
		$this->cache->set("key3", 3);

		$this->cache->get("key1");
		$this->cache->get("key2");
		$this->cache->set("key4", 4); // should evict key3

		$this->cache->get("key1");
		$this->cache->set("key5", 5); // should evict key2

		$this->cache->set("key6", 6); // should evict key4

		$this->assertEquals($this->cache->size(), 3);

		$this->assertEquals($this->cache->get("key6"), 6);
		$this->assertEquals($this->cache->get("key5"), 5);
		$this->assertEquals($this->cache->get("key1"), 1);
		$this->assertNull($this->cache->get("key2"));
		$this->assertNull($this->cache->get("key3"));
		$this->assertNull($this->cache->get("key4"));
	}
}