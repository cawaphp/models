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

namespace Cawa\Models\Commons;

use Cawa\Orm\Event as EventBase;
use Cawa\Orm\Model;

class Event extends EventBase
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param string $name
     * @param Model $model
     * @param int $userId
     * @param array $data
     */
    public function __construct($name, Model $model, int $userId = null, array $data = [])
    {
        $this->userId = $userId;
        parent::__construct($name, $model, $data);
    }
}
