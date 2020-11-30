<?php
use PHPUnit\Framework\TestCase;
use My\Memcache;
use My\CacheFullException;
final class MemcacheTest extends TestCase
{
	public function testSetAndGet()
	{
		$cache = new Memcache();
		$cache->set("test_key", "test_value");

		$this->assertEquals($cache->get("test_key"), "test_value");

		$this->assertNull($cache->get("non-existent-key"));
	}

	public function testUnset()
	{
		$cache = new Memcache();
		$cache->set("test_key", "test_value");

		$this->assertEquals($cache->get("test_key"), "test_value");
		$cache->unset("test_key");
		$this->assertNull($cache->get("test_key"));
	}

	public function testOverflow()
	{
		$cache = new Memcache(/* file */null, 2);
		$cache->set("key1", 1);
		$cache->set("key2", 2);

		$this->expectException(CacheFullException::class);
		$cache->set("key3", 3);
	}

	public function testOverflowUnset()
	{
		$cache = new Memcache(/* file */null, 2);
		$cache->set("key1", 1);
		$cache->set("key2", 2);

		$cache->unset("key1");
		$cache->set("key3", 3);

		$this->assertEquals($cache->get("key3"), 3);
	}

	public function testPersistence()
	{
		$cache = new Memcache("test-cache.db");
		$cache->set(1, 1);
		unset($cache);

		$cache = new Memcache("test-cache.db");
		$this->assertEquals($cache->get(1), 1);
	}
}