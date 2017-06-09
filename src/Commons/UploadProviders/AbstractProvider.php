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

use Cawa\Models\Commons\Upload;

abstract class AbstractProvider extends Upload
{
    //region Constants

    const PROVIDER_FILESYSTEM = 'FILESYSTEM';
    const PROVIDER_DATABASE = 'DATABASE';
    const PROVIDER_OPENSTACK = 'OPENSTACK';

    // endregion

    /**
     * @return string
     */
    abstract public function getContent() : string;

    /**
     * @param string $content
     *
     * @return mixed
     */
    abstract public function saveContent(string $content);

    /**
     * @return string
     */
    protected function getDefaultPath() : string
    {
        if (!$this->id) {
            throw new \LogicException('Attempt to get default path without id');
        }

        return '/' . date('Y/m/d') . '/' . $this->id . '.' . $this->extension;
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
