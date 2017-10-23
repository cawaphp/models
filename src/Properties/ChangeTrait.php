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

namespace Cawa\Models\Properties;

use Cawa\Date\DateTime;
use Cawa\Models\Commons\Change;
use Cawa\Orm\Collection;

trait ChangeTrait
{
    /**
     * @var array
     */
    private $changesType = [];

    /**
     * @param string $operation
     *
     * @return Collection|Change[]
     */
    private function getChangeByOperation(string $operation)
    {
        if (!isset($this->changesType[$operation])) {
            $this->changesType[$operation] = Change::getByTypeAndOperationAndExternalId(self::MODEL_TYPE, $operation, $this->getId());
        }

        return $this->changesType[$operation];
    }

    /**
     * @var Collection
     */
    private $changesHistory;

    /**
     * @return Collection|Change[]
     */
    public function getChangeHistory()
    {
        if (!isset($this->changesHistory)) {
            $this->changesHistory = Change::getByTypeAndExternalId(self::MODEL_TYPE, $this->getId());
        }

        return $this->changesHistory;
    }

    /**
     * @return int|null
     */
    public function getAddedUserId()
    {
        return $this->getChangeByOperation(Change::OPERATION_INSERT)[0] ?
            $this->getChangeByOperation(Change::OPERATION_INSERT)[0]->getUserId() :
            null;
    }

    /**
     * @return string|null
     */
    public function getAddedIp()
    {
        return $this->getChangeByOperation(Change::OPERATION_INSERT)[0] ?
            $this->getChangeByOperation(Change::OPERATION_INSERT)[0]->getIp() :
            null;
    }

    /**
     * @return DateTime|null
     */
    public function getAddedDate()
    {
        return $this->getChangeByOperation(Change::OPERATION_INSERT)[0] ?
            $this->getChangeByOperation(Change::OPERATION_INSERT)[0]->getDate() :
            null;
    }
}
