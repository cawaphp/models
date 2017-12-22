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
use Cawa\Events\DispatcherFactory;
use Cawa\Models\Properties\UserTrait;
use Cawa\Orm\CollectionModel;
use Cawa\Orm\Model;

class Auth extends Model
{
    use DatabaseFactory;
    use DispatcherFactory;
    use UserTrait;

    //region Constants

    const MODEL_TYPE = 'AUTH';

    /**
     * Password login.
     */
    const TYPE_PASSWORD = 'PASSWORD';

    /**
     * Token login.
     */
    const TYPE_TOKEN = 'TOKEN';

    /**
     * Socials : Facebook.
     */
    const TYPE_FACEBOOK = 'FACEBOOK';

    /**
     * Socials : Google.
     */
    const TYPE_GOOGLE = 'GOOGLE';

    /**
     * Socials : Twitter.
     */
    const TYPE_TWITTER = 'TWITTER';

    /**
     * Socials : Yahoo.
     */
    const TYPE_YAHOO = 'YAHOO';

    /**
     * Socials : Microsoft
     */
    const TYPE_MICROSOFT = 'MICROSOFT';

    /**
     * Socials : Microsoft Live
     */
    const TYPE_LIVE = 'LIVE';

    //endregion

    //region Mutator

    /**
     * @var int
     */
    private $id;

    /**
     * @return int
     */
    public function getId()
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
     * @var string
     */
    private $uid;

    /**
     * @return string
     */
    public function getUid() : string
    {
        return $this->uid;
    }

    /**
     * @param string $Uid
     *
     * @return $this|self
     */
    public function setUid(string $Uid) : self
    {
        $this->uid = $Uid;

        return $this;
    }

    /**
     * @var array
     */
    private $data = [];

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
        if ($this->data !== $data) {
            $this->data = $data;

            $this->addChangedProperties('data', $this->data);
        }

        return $this;
    }

    /**
     * @var DateTime
     */
    private $loginDate;

    /**
     * @return DateTime
     */
    public function getLoginDate() : ?DateTime
    {
        return $this->loginDate;
    }

    //endregion

    //region Logic

    /**
     * return a user id token.
     *
     * @return string
     */
    public function generateToken() : string
    {
        return base64_encode(random_bytes(64));
    }

    //endregion

    //region Db Read

    /**
     * @param int $id
     *
     * @return CollectionModel|$this[]
     */
    public static function getByUserId(int $id) : CollectionModel
    {
        $return = [];

        $db = self::db(self::class);
        $sql = 'SELECT *
                FROM tbl_users_auth
                WHERE auth_user_id = :userId
                    AND auth_deleted IS NULL';
        foreach ($db->query($sql, ['userId' => $id]) as $result) {
            $item = new static();
            $item->map($result);
            $return[] = $item;
        }

        $collection = new CollectionModel($return);

        return $collection;
    }

    /**
     * @param int $id
     * @param string $type
     *
     * @return self|$this
     */
    public static function getByUserIdAndType(int $id, string $type) : ?self
    {
        $db = self::db(self::class);

        $sql = 'SELECT *
                FROM tbl_users_auth
                WHERE auth_user_id = :userId
                    AND auth_type = :type
                    AND auth_deleted IS NULL
                !forUpdate';

        $result = $db->fetchOne($sql, [
            'userId' => $id,
            'type' => $type,
            '!forUpdate' => $db->isTransactionStarted() ? 'FOR UPDATE' : '',
        ]);

        if ($result) {
            $return = new static();
            $return->map($result);

            return $return;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function map(array $result)
    {
        $this->id = $result['auth_id'];
        $this->userId = $result['auth_user_id'];
        $this->type = $result['auth_type'];
        $this->uid = $result['auth_uid'];
        $this->data = $result['auth_data'] ? self::decodeData($result['auth_data']) : [];
        $this->loginDate = $result['auth_login_date'];
    }

    //endregion

    //region Db Alter

    /**
     * @param User $user
     *
     * @return bool
     */
    public function insert(User $user)
    {
        $this->userId = $user->getId();

        $db = self::db(self::class);
        $started = $db->startTransactionIf();

        if ($auth = self::getByUserIdAndType($this->userId, $this->type)) {
            throw new \RuntimeException(sprintf(
                "Auth '%s' for user '%s' is already set",
                $this->type,
                $this->userId
            ));
        }

        $sql = 'INSERT INTO tbl_users_auth
                SET auth_user_id = :userId,
                auth_type = :type,
                auth_uid = :uid,
                auth_data = :data';

        $result = $db->query($sql, [
            'userId' => $this->userId,
            'type' => $this->type,
            'uid' => $this->uid,
            'data' => $this->data ? self::encodeData($this->data) : null,
        ]);

        $this->id = (int) $result->insertedId();

        unset($this->changedProperties['data']);

        if (!$started) {
            $db->commit();
        }

        return true;
    }

    /**
     * @return bool
     */
    public function update()
    {
        $db = self::db(self::class);

        $sql = 'UPDATE tbl_users_auth
                SET auth_data = :data
                WHERE auth_id = :id';

        $db->query($sql, [
            'id' => $this->id,
            'data' => $this->data ? self::encodeData($this->data) : null,
        ]);

        unset($this->changedProperties['data']);

        return true;
    }

    /**
     */
    public function updateLoginDate()
    {
        $db = self::db(self::class);

        $sql = 'UPDATE tbl_users_auth
                SET auth_login_date = :date
                WHERE auth_id = :id';

        $db->query($sql, [
            'id' => $this->id,
            'date' => new DateTime(),
        ]);
    }

    //endregion
}
