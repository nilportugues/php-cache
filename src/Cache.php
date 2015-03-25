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
	* @param CacheAdapter|null $cache
	*/
	public function __construct(CacheAdapter $cache = null)
	{
		$this->cache = (null === $cache) ? new InMemoryAdapter() : $cache;
	}
}
