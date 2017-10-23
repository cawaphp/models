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

use Cawa\Events\TimerEvent;
use Cawa\Orm\Collection;

trait ParentTrait
{
    /**
     * @var int
     */
    private $parentId;

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param int $parentId
     *
     * @return $this|self
     */
    public function setParentId(int $parentId = null) : self
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @var self
     */
    private $parent;

    /**
     * @return $this|self|static
     */
    public function getParent() : ?self
    {
        if (!$this->parent && $this->parentId) {
            $this->parent = self::getById($this->parentId);
        }

        return $this->parent;
    }

    /**
     * @var Collection|$this[]
     */
    private $childs;

    /**
     * @return $this[]|Collection
     */
    public function getChilds()
    {
        if (!$this->childs) {
            $this->childs = self::getByParentId($this->id);
        }

        return $this->childs;
    }

    /**
     * @return int
     */
    public function getLevel() : int
    {
        $current = $this;
        $count = 1;
        while (!is_null($current->getParentId())) {
            $count++;

            $current = $current->getParent();
        }

        return $count;
    }

    /**
     * @param static[]|Collection $collection
     *
     * @return Collection|static[]
     */
    public static function sortChild(Collection $collection) : Collection
    {
        $timerEvent = new TimerEvent('parent.sortChild', [
            'type' => get_called_class(),
            'size' => $collection->count(),
        ]);

        $all = $collection->partition(function ($current) {
            /* @var ParentTrait $current */
            return is_null($current->getParentId());
        });

        list($noParent, $withParent) = $all;

        $recursive = function (Collection $current, Collection &$all) use (&$recursive) {
            foreach ($current as $item) {
                list($childs, $all) = $all->partition(function ($element) use ($item) {
                    /* @var $element self */
                    return $element->getParentId() == $item->getId();
                });

                $item->childs = $childs;
                foreach ($item->childs as $child) {
                    $child->parent = $item;
                }

                $recursive($item->childs, $all);
            }
        };

        $recursive($noParent, $withParent);

        self::emit($timerEvent);

        return $noParent;
    }

    /**
     * @param static[]|Collection $collection
     *
     * @return Collection|static[]
     */
    public static function fillChildAndParent(Collection $collection) : Collection
    {
        /** @var static[]|Collection $collection */
        $collection = clone $collection;

        /** @var static|self|$this $item */
        foreach ($collection as $item) {
            $element = $item;

            while ($element->parentId) {
                $data[] = $element->parent = $collection->findOne('getId', $element->parentId);
                $element = $element->getParent();
            }

            $item->childs = $collection->find('getParentId', $item->id);
        }

        return $collection;
    }

    /**
     * @return Collection|self[]
     */
    public function getParentTree() : Collection
    {
        $data = [];

        $element = $this;
        while ($element->parentId) {
            $data[] = $element->getParent();
            $element = $element->getParent();
        }

        $data = array_reverse($data);
        $data[] = $this;

        return new Collection($data);
    }

    /**
     * @return int[]|array
     */
    public function getParentTreeIds() : array
    {
        $data = [];

        foreach ($this->getParentTree() as $tree) {
            $data[] = $tree->getId();
        }

        return $data;
    }

    /**
     * @param string $locale
     *
     * @return string[]|array
     */
    public function getParentTreeName(string $locale) : array
    {
        $data = [];

        foreach ($this->getParentTree() as $tree) {
            $data[] = $tree->getName($locale);
        }

        return $data;
    }
}
