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

namespace Cawa\Models\Commons\UploadProviders;

use Cawa\App\AbstractApp;
use Cawa\Cache\CacheFactory;
use Cawa\Models\Commons\Upload;

abstract class AbstractProvider extends Upload
{
    use CacheFactory;

    //region Constants

    const PROVIDER_FILESYSTEM = 'FILESYSTEM';
    const PROVIDER_DATABASE = 'DATABASE';
    const PROVIDER_OPENSTACK = 'OPENSTACK';

    // endregion

    public function getContent() : string
    {
        $cache = self::cache(Upload::class);

        if ($content = $cache->get($cacheKey = 'upload/id/' . $this->id)) {
            /** @noinspection PhpStrictTypeCheckingInspection */
            return $content;
        }

        $content = $this->getProviderContent();

        $cache->set($cacheKey, $content);

        return $content;
    }

    /**
     * @param string $content
     *
     * @return mixed
     */
    public function saveContent(string $content)
    {
        $cache = self::cache(Upload::class);
        $cache->delete('upload/id/' . $this->id);

        return $this->saveProviderContent($content);
    }

    /**
     * @return string
     */
    abstract protected function getProviderContent() : string;

    /**
     * @param string $content
     *
     * @return mixed
     */
    abstract protected function saveProviderContent(string $content);

    /**
     * @return string
     */
    protected function getDefaultPath() : string
    {
        if (!$this->id) {
            throw new \LogicException('Attempt to get default path without id');
        }

        return '/' . strtolower(AbstractApp::env()) . '/' .
            $this->getDate()->format('Y/m/d') .
            '/' . $this->id . '.' . $this->extension
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function save(int $externalId = null) : bool
    {
        $return = parent::save($externalId);

        if ($this->content) {
            $this->saveContent($this->content);
        }

        return $return;
    }
}
