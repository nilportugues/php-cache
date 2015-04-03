<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/3/15
 * Time: 1:17 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Cache\Adapter;

use NilPortugues\Cache\CacheAdapter;

/**
 * Class Adapter
 * @package NilPortugues\Cache\Adapter
 */
abstract class Adapter implements CacheAdapter
{
    /**
     * @var bool
     */
    protected $hit = false;

    /**
     * @var null|int
     */
    protected $ttl;

    /**
     * Check if value was found in the cache or not.
     *
     * @return bool
     */
    public function isHit()
    {
        return $this->hit;
    }

    /**
     * Allows to set a default ttl value if none is provided for set()
     *
     * @param  int $ttl
     *
     * @throws \InvalidArgumentException
     * @return bool|mixed
     */
    public function defaultTtl($ttl)
    {
        if (false === is_numeric($ttl)) {
            throw new \InvalidArgumentException('A TTL value must be an integer value');
        }

        $this->ttl = (int)$ttl;
    }


    /**
     * @param $ttl
     *
     * @return int|null
     */
    protected function fromDefaultTtl($ttl)
    {
        $ttl = (int) $ttl;

        if (0 == $ttl && null !== $this->ttl) {
            $ttl = $this->ttl;
        }
        return $ttl;
    }

    /**
     * @param $value
     *
     * @return string
     */
    protected function storageDataStructure($value)
    {
        return serialize($value);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    protected function restoreDataStructure($value)
    {
        return unserialize($value);
    }
}
