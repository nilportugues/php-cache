<?php
namespace NilPortugues\Cache;

use NilPortugues\Cache\Adapter\InMemoryAdapter;

/**
 * 
 */
final class Cache implements CacheAdapter
{
	/**
	* @var CacheAdapter  
	*/
	private $cache;
	
	/**
	 * @var string
	 */
	private $namespace;

	/**
	 * @var int
	 */
	private $expires;
	
	/**
	* @param CacheAdapter|null $cache
	*/
	public function __construct(CacheAdapter $cache = null, $namespace = '', $expires = 0)
	{
		$this->cache = (null === $cache) ? new InMemoryAdapter() : $cache;
		$this->namespace = (empty($namespace)) ? '' : $namespace.".";
		$this->expires = (int) $expires;
	}
	
	public function get($key)
	{
		return $this->cache->get($this->namespace.$key);
	}
	
	public function set($key, $value, $ttl = null)
	{
		$ttl = (null === $ttl) ? $this->expires : $ttl;
		$this->cache->set($this->namespace.$key, $value, $ttl);
		return $this;
	}
}
