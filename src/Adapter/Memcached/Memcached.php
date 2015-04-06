<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/6/15
 * Time: 6:31 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Cache\Adapter\Memcached;

use \Memcached as MemcachedDriver;

/**
 * Class Memcached
 * @package NilPortugues\Cache\Adapter\Memcached
 */
class Memcached implements MemcachedClient
{
    /**
     * @param       $persistentId
     * @param array $connections
     */
    public function __construct($persistentId, array $connections)
    {
        $this->isMemcachedExtensionAvailable();

        $this->memcached = new MemcachedDriver($persistentId);
        $this->memcached->addServers($connections);

        $this->memcached->setOption(
            MemcachedDriver::OPT_SERIALIZER,
            (defined(MemcachedDriver::HAVE_IGBINARY) && MemcachedDriver::HAVE_IGBINARY)
                ? MemcachedDriver::SERIALIZER_IGBINARY : MemcachedDriver::SERIALIZER_PHP
        );

        $this->memcached->setOption(MemcachedDriver::OPT_DISTRIBUTION, MemcachedDriver::DISTRIBUTION_CONSISTENT);
        $this->memcached->setOption(MemcachedDriver::OPT_LIBKETAMA_COMPATIBLE, true);
        $this->memcached->setOption(MemcachedDriver::OPT_BINARY_PROTOCOL, true);
    }


    /**
     * @throws \Exception
     * @codeCoverageIgnore
     */
    private function isMemcachedExtensionAvailable()
    {
        if (false === class_exists('\Memcached')) {
            throw new \Exception('Memcached extension for PHP is not installed on the system.');
        }
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->memcached->get($key);
    }

    /**
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function set($key, $value)
    {
        return $this->memcached->set($key, $value);
    }

    /**
     * @param $key
     * @param $expiration
     */
    public function touch($key, $expiration)
    {
        $this->memcached->touch($key, $expiration);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function delete($key)
    {
        return $this->memcached->delete($key);
    }

    /**
     * @return mixed
     */
    public function getStats()
    {
        return $this->memcached->getStats();
    }

    /**
     * @return mixed
     */
    public function flush()
    {
        return $this->memcached->flush();
    }
}
