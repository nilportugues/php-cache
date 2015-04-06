<?php
/**
 * Author: Nil Portugués Calderó <contact@nilportugues.com>
 * Date: 4/6/15
 * Time: 8:49 PM
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NilPortugues\Tests\Cache\Adapter\ElasticSearch;

use NilPortugues\Cache\Adapter\ElasticSearchAdapter;

/**
 * Class DummyElasticSearchAdapter
 * @package NilPortugues\Tests\Cache\Adapter\ElasticSearch
 */
class DummyElasticSearchAdapter extends ElasticSearchAdapter
{
    /**
     * @codeCoverageIgnore
     * @return DummyCurl
     */
    protected function getCurlClient()
    {
        return new DummyCurl();
    }
}
