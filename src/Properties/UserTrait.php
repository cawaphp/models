<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Cawa\Models\Properties;

trait UserTrait
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * @return int
     */
    public function getUserId() : ?int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return $this|self
     */
    public function setUserId(int $userId = null)
    {
        if ($this->userId !== $userId) {
            $this->userId = $userId;

            $this->addChangedProperties('userId', $userId);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasUser() : bool
    {
        return !is_null($this->userId);
    }
}
