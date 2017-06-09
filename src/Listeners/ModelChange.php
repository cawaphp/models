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

namespace Cawa\Models\Listeners;

use Cawa\Models\Commons\Change;
use Cawa\Models\Commons\Event;
use Cawa\Net\Ip;

class ModelChange
{
    /**
     * @param Event $event
     *
     * @return bool
     */
    public static function receive(Event $event)
    {
        if ($event->getName() != 'model.insert' &&
            $event->getName() != 'model.update' &&
            $event->getName() != 'model.delete'
        ) {
            return false;
        }

        if (sizeof($event->getModel()->getChangedProperties()) == 0 && $event->getName() == 'model.update') {
            return true;
        }

        $change = new Change();

        $return = $change->setType(constant(get_class($event->getModel()) . '::MODEL_TYPE'))
            ->setExternalId($event->getModel()->getId())
            ->setOperation(strtoupper(substr($event->getName(), strpos($event->getName(), '.') + 1)))
            ->setIp(Ip::get())
            ->setData($event->getModel()->getChangedProperties())
            ->setUserId($event->getUserId())
            ->insert();

        $event->getModel()->resetChangedProperties();

        return $return;
    }
}
