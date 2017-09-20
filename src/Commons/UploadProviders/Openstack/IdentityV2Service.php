<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Cawa\Models\Commons\UploadProviders\Openstack;

use Cawa\Cache\CacheFactory;
use Cawa\Core\DI;
use Cawa\Date\DateTime;
use OpenStack\Identity\v2\Models\Token;
use OpenStack\Identity\v2\Service;

class IdentityV2Service extends Service
{
    use CacheFactory;

    /**
     * {@inheritdoc}
     */
    public function authenticate(array $options = []) : array
    {
        $cache = self::cache(self::class);
        $cacheKey = md5(serialize(DI::config()->get('upload/providerOptions/OPENSTACK')));
        $cacheData = $cache->get($cacheKey);

        if (!$cacheData) {
            $return = parent::authenticate($options);

            /** @var $token Token */
            list($token, $serviceUrl) = $return;

            $expireTtl = (new DateTime())->diffInSeconds(DateTime::createFromTimestamp($token->expires->getTimestamp()));

            $cache->set($cacheKey, [$token->serialize(), $serviceUrl], $expireTtl - (60 * 60));
        } else {
            list($tokenSerialize, $serviceUrl) = $cacheData;

            $token = $this->model(Token::class);
            $token->populateFromArray((array) $tokenSerialize);
        }

        return [$token, $serviceUrl];
    }
}
