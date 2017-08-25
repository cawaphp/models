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
use Cawa\Core\DI;

class Filesystem extends AbstractProvider
{
    /**
     * @return string
     */
    private function getStoragePath() : string
    {
        return AbstractApp::getAppRoot() .
            DI::config()->get('upload/path') .
            $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent() : string
    {
        return file_get_contents($this->getStoragePath());
    }

    /**
     * {@inheritdoc}
     */
    public function saveContent(string $content) : bool
    {
        if (!$this->path) {
            $this->setPath($this->getDefaultPath());
            $this->save();
        }

        if (!file_exists(dirname($this->getStoragePath()))) {
            mkdir(dirname($this->getStoragePath()), 0777, true);
        }

        file_put_contents($this->getStoragePath(), $content);

        return true;
    }
}
