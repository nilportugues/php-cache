<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 12/9/15
 * Time: 4:41 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Cache\Adapter;

/**
 * Class InMemoryAdapterFactory
 * @package NilPortugues\Cache\Adapter
 */
class InMemoryAdapterFactory
{
    /**
     * @return InMemoryAdapter
     */
    public static function create()
    {
        return InMemoryAdapter::getInstance();
    }
} 