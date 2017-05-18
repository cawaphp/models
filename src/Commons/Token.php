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

use Cawa\Date\DateTime;
use Cawa\Db\DatabaseFactory;
use Cawa\Orm\Model;

class Token extends Model
{
    use DatabaseFactory;

    //region Constants

    const EMAIL_CONFIRMATION = 'EMAIL_CONFIRMATION';
    const CHANGE_PASSWORD = 'CHANGE_PASSWORD';

    //endregion

    //region Mutator

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
    private $token;

    /**
     * @return string
     */
    public function getToken() : string
    {
        return $this->token;
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
        return $this->data ?? [];
    }

    /**
     * @param array $data
     *
     * @return Token
     */
    public function setData(array $data) : Token
    {
        $this->data = $data;

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

    //endregion

    //region Db Read

    /**
     * @param string $token
     *
     * @return $this|self|null
     */
    public static function getByToken(string $token)
    {
        $db = self::db(self::class);

        $sql = 'SELECT * 
                FROM tbl_commons_token
                WHERE token_token = :token';
        if ($result = $db->fetchOne($sql, ['token' => $token])) {
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
        $this->type = $result['token_type'];
        $this->externalId = $result['token_external_id'];
        $this->date = $result['token_date'];
        $this->token = $result['token_token'];
        $this->data =  $result['token_data'] ?
            self::decodeData($result['token_data']) :
            $result['token_data'];
    }

    //endregion

    //region Db Alter

    /**
     * @param bool $readable
     *
     * @return bool
     */
    public function insert(bool $readable = false) : bool
    {
        $db = self::db(self::class);

        $this->date = new DateTime();

        if ($readable) {
            $this->token = (string) mt_rand(100000, 999999);
        } else {
            $this->token = md5(uniqid((string) random_bytes(128), true));
        }

        $sql = 'REPLACE INTO tbl_commons_token
                SET token_type = :type, 
                    token_external_id = :externalId,
                    token_date = :date,
                    token_token = :token,
                    token_data = :data';

        $db->query($sql, [
            'type' => $this->type,
            'externalId' => $this->externalId,
            'date' => $this->date,
            'token' => $this->token,
            'data' => $this->data ? self::encodeData($this->data) : null,
        ]);

        return true;
    }

    /**
     * @return bool
     */
    public function delete()
    {
        $db = self::db(self::class);

        $sql = 'DELETE FROM tbl_commons_token
                WHERE token_type = :type 
                  AND token_external_id = :externalId';

        $result = $db->query($sql, [
            'type' => $this->type,
            'externalId' => $this->externalId,
        ]);

        return $result->affectedRows() > 0;
    }

    //endregion
}
