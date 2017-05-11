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

namespace Cawa\Models\Users;

use Cawa\Db\DatabaseFactory;
use Cawa\Models\Commons\Change;
use Cawa\Orm\CollectionModel;
use Cawa\Orm\Model;

class Status extends Model
{
    use DatabaseFactory;

    //region Constants

    const MODEL_TYPE = 'STATUS';

    /**
     * Status: Email verification is done
     */
    const VERIFIED = 'VERIFIED';

    //endregion

    //region Mutator

    /**
     * @var int
     */
    private $id;

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @var string
     */
    private $status;

    /**
     * @return string
     */
    public function getStatus() : string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return $this|self
     */
    public function setStatus(string $status) : self
    {
        if ($this->status !== $status) {
            $this->status = $status;

            $this->addChangedProperties('status', $status);
        }

        return $this;
    }

    /**
     * @var int
     */
    private $value;

    /**
     * @return int
     */
    public function getValue() : int
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @return $this|self
     */
    public function setValue(int $value) : self
    {
        if ($this->value !== $value) {
            $this->value = $value;

            $this->addChangedProperties('value', $value);
        }

        return $this;
    }

    //endregion

    /**
     * @param string $status
     * @param int $value
     */
    public function __construct(string $status = null, int $value = null)
    {
        if ($status) {
            $this->setStatus($status);
        }

        if ($value) {
            $this->setValue($value);
        }
    }

    //region Db Read

    /**
     * @param int $userId
     *
     * @return CollectionModel|$this[]
     */
    public static function getByUserId(int $userId) : CollectionModel
    {
        $return = [];
        $db = self::db(self::class);
        $sql = 'SELECT *
                FROM tbl_users_status
                WHERE status_user_id = :userId';
        foreach ($db->query($sql, ['userId' => $userId]) as $result) {
            $item = new static();
            $item->map($result);
            $return[] = $item;
        }

        $collection = new CollectionModel($return);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function map(array $result)
    {
        $this->id = $result['status_id'];
        $this->status = $result['status_status'];
        $this->value = $result['status_value'];
    }

    //endregion

    //region Db Alter

    /**
     * @param User $user
     *
     * @return bool
     */
    public function insert(User $user) : bool
    {
        $db = self::db(self::class);

        $sql = 'INSERT INTO tbl_users_status
                SET status_user_id = :userId,
                    status_status = :status,
                    status_value = :value';

        $result = $db->query($sql, [
            'userId' => $user->getId(),
            'status' => $this->status,
            'value' => $this->value,
        ]);

        $this->id = $result->insertedId();

        $this->changedProperties['operation'] = Change::OPERATION_INSERT;
        $this->changedProperties['id'] = $this->id;

        return true;
    }

    /**
     * @return bool
     */
    public function update() : bool
    {
        $db = self::db(self::class);

        $sql = 'UPDATE tbl_users_status
                SET status_value = :value
                WHERE status_id = :id';

        $db->query($sql, [
            'id' => $this->id,
            'value' => $this->value,
        ]);

        $this->changedProperties['operation'] = Change::OPERATION_UPDATE;
        $this->changedProperties['id'] = $this->id;

        return true;
    }

    /**
     * @return bool
     */
    public function delete() : bool
    {
        $db = self::db(self::class);

        $sql = 'DELETE FROM tbl_users_status
                WHERE status_id = :id';

        $db->query($sql, [
            'id' => $this->id,
        ]);

        $this->changedProperties['operation'] = Change::OPERATION_DELETE;
        $this->changedProperties['id'] = $this->id;

        return true;
    }

    //endregion
}
