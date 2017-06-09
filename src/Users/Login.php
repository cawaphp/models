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

namespace Cawa\Models\Users;

use Cawa\Date\DateTime;
use Cawa\Db\DatabaseFactory;
use Cawa\Net\Ip;
use Cawa\Orm\Model;

class Login extends Model
{
    use DatabaseFactory;

    const MODEL_TYPE = 'LOGIN';

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
     * @var int
     */
    private $userId;

    /**
     * @return int
     */
    public function getUserId() : int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return $this|self
     */
    public function setUserId(int $userId) : self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @var int
     */
    private $authId;

    /**
     * @return int
     */
    public function getAuthId() : int
    {
        return $this->authId;
    }

    /**
     * @param int $authId
     *
     * @return $this|self
     */
    public function setAuthId(int $authId) : self
    {
        $this->authId = $authId;

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
    public function setIp(string $ip) : self
    {
        $this->ip = $ip;

        return $this;
    }

    //endregion

    //region Db Read

    /**
     * {@inheritdoc}
     */
    public function map(array $result)
    {
        $this->id = $result['auth_id'];
        $this->userId = $result['auth_user_id'];
        $this->authId = $result['auth_auth_id'];
        $this->date = $result['auth_date'];
        $this->ip = Ip::fromLong($result['auth_ip']);
    }

    //endregion

    //region Db Alter

    /**
     * @return bool
     */
    public function insert()
    {
        $this->date = new DateTime();

        $db = self::db(self::class);

        $sql = 'INSERT INTO tbl_users_login
                SET login_user_id = :userId,
                login_auth_id = :authId,
                login_date = :date,
                login_ip = :ip';

        $result = $db->query($sql, [
            'userId' => $this->userId,
            'authId' => $this->authId,
            'date' => $this->date,
            'ip' => Ip::toLong($this->ip),
        ]);

        $this->id = (int) $result->insertedId();

        return true;
    }

    //endregion
}
