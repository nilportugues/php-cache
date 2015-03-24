<?php
namespace NilPortugues\Cache;

final class Cache implements CacheAdapter
{
  private $cache;
  
  public function __construct(CacheAdapter $cache)
  {
    $this->cache = $cache;
  }
}
