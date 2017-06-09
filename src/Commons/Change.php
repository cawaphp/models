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

namespace Cawa\Models\Commons;

use Cawa\Date\DateTime;
use Cawa\Db\DatabaseFactory;
use Cawa\Models\Properties\UserTrait;
use Cawa\Net\Ip;
use Cawa\Orm\Collection;
use Cawa\Orm\Model;

class Change extends Model
{
    use DatabaseFactory;
    use UserTrait;

    //region Constants

    const OPERATION_INSERT = 'INSERT';
    const OPERATION_UPDATE = 'UPDATE';
    const OPERATION_DELETE = 'DELETE';

    // endregion

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
    private $type;

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this|self
     */
    public function setType(string $type) : self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @var int
     */
    private $externalId;

    /**
     * @return int
     */
    public function getExternalId() : int
    {
        return $this->externalId;
    }

    /**
     * @param int $externalId
     *
     * @return $this|self
     */
    public function setExternalId(int $externalId) : self
    {
        $this->externalId = $externalId;

        return $this;
    }

    /**
     * @var string
     */
    private $operation;

    /**
     * @return string
     */
    public function getOperation() : string
    {
        return $this->operation;
    }

    /**
     * @param string $operation
     *
     * @return $this|self
     */
    public function setOperation(string $operation) : self
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * @var DateTime
     */
    private $date;

    /**
     * @return DateTime
     */
    public function getDate() : DateTime
    {
        return $this->date;
    }

    /**
     * @var string
     */
    private $ip;

    /**
     * @return string
     */
    public function getIp() : string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     *
     * @return $this|self
     */
    public function setIp(string $ip = null) : self
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * @var array
     */
    private $data;

    /**
     * @return array
     */
    public function getData() : array
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this|self
     */
    public function setData(array $data) : self
    {
        $this->data = $data;

        return $this;
    }

    //endregion

    //region Db Read

    /**
     * {@inheritdoc}
     */
    public function map(array $result)
    {
        $this->id = $result['change_id'];
        $this->externalId = $result['change_external_id'];
        $this->type = $result['change_type'];
        $this->operation = $result['change_operation'];
        $this->date = $result['change_date'];
        $this->ip = $result['change_ip'] ? Ip::fromLong($result['change_ip']) : null;
        $this->userId = $result['change_user_id'];
        $this->data = $result['change_data'] ? self::decodeData($result['change_data']) : [];
    }

    /**
     * @param string $type
     * @param int $externalId
     *
     * @return $this[]|Collection
     */
    public static function getByTypeAndExternalId(string $type, int $externalId) : Collection
    {
        $return = [];

        $db = self::db(self::class);
        $sql = 'SELECT *
                FROM tbl_commons_change
                WHERE change_type = :type
                    AND change_external_id = :externalId
                    AND change_deleted IS NULL';
        foreach ($db->query($sql, ['type' => $type, 'externalId' => $externalId]) as $result) {
            $item = new static();
            $item->map($result);
            $return[] = $item;
        }

        $collection = new Collection($return);

        return $collection;
    }

    /**
     * @param string $type
     * @param string $operation
     * @param int $externalId
     *
     * @return $this[]|Collection
     */
    public static function getByTypeAndOperationAndExternalId(string $type, string $operation, int $externalId) : Collection
    {
        $return = [];

        $db = self::db(self::class);
        $sql = 'SELECT *
                FROM tbl_commons_change
                WHERE change_type = :type
                    AND change_operation = :operation
                    AND change_external_id = :externalId
                    AND change_deleted IS NULL';
        foreach ($db->query($sql, ['type' => $type, 'operation' => $operation, 'externalId' => $externalId]) as $result) {
            $item = new static();
            $item->map($result);
            $return[] = $item;
        }

        $collection = new Collection($return);

        return $collection;
    }

    /**
     * @param int $id
     *
     * @return $this|self|null
     */
    public static function getById(int $id)
    {
        $db = self::db(self::class);

        $sql = 'SELECT *
                FROM tbl_commons_change
                WHERE change_id = :id
                    AND change_deleted IS NULL';
        if ($result = $db->fetchOne($sql, ['id' => $id])) {
            $return = new static();
            $return->map($result);

            return $return;
        }

        return null;
    }

    //endregion

    //region Db Alter

    /**
     * @return bool
     */
    public function insert() : bool
    {
        $db = self::db(self::class);

        $this->date = new DateTime();

        $sql = 'INSERT INTO tbl_commons_change
                SET change_external_id = :externalId,
                    change_type = :type,
                    change_operation = :operation,
                    change_date = :date,
                    change_ip = :ip,
                    change_user_id = :userId,
                    change_data= :data';

        $result = $db->query($sql, [
            'externalId' => $this->externalId,
            'type' => $this->type,
            'operation' => $this->operation,
            'date' => $this->date,
            'ip' => $this->ip ? Ip::toLong($this->ip) : null,
            'userId' => $this->userId,
            'data' => $this->data ? self::encodeData($this->data) : null,
        ]);

        $this->id = $result->insertedId();

        return true;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $db = self::db(self::class);

        $sql = 'UPDATE tbl_commons_change
                SET change_deleted = NOW()
                WHERE change_id = :id';

        $db->query($sql, [
            'id' => $this->id,
        ]);

        return true;
    }

    //endregion
}
