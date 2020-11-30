# MyMemcache

Implements a simple memory-based cache and extends it with LRU eviction policy.
The LRU uses timestamps and a min-heap to remove least recently used elements.

The cache persists to file on exit so that it can be resumed later.

## Testing
Test with the following command:

```
phpunit --bootstrap common.php tests/*
```


## Dependencies
Uses ReactPHP for persistent web-server, uses PHPUnit for automated testing.