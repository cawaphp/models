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
use Cawa\GoogleMaps\Models\GeocoderResult;
use Cawa\Intl\TranslatorFactory;
use Cawa\Models\Commons\Change;
use Cawa\Models\Properties\UserTrait;
use Cawa\Orm\CollectionModel;
use Cawa\Orm\Model;

class Address extends Model
{
    use DatabaseFactory;
    use DispatcherFactory;
    use TranslatorFactory;
    use UserTrait;

    const MODEL_TYPE = 'ADDRESS';

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
     * @var bool
     */
    private $main = false;

    /**
     * @return bool
     */
    public function isMain() : bool
    {
        return $this->main;
    }

    /**
     * @param bool $main
     *
     * @return $this|self
     */
    public function setMain(bool $main) : self
    {
        if ($this->main != $main) {
            $this->main = $main;

            $this->addChangedProperties('main', $main);
        }

        return $this;
    }

    /**
     * @var string
     */
    private $firstName;

    /**
     * @return string
     */
    public function getFirstName() : string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return $this|self
     */
    public function setFirstName(string $firstName) : self
    {
        if ($this->firstName != $firstName) {
            $this->firstName = $firstName;

            $this->addChangedProperties('society', $firstName);
        }

        return $this;
    }

    /**
     * @var string
     */
    private $lastName;

    /**
     * @return string
     */
    public function getLastName() : string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return $this|self
     */
    public function setLastName(string $lastName) : self
    {
        if ($this->lastName != $lastName) {
            $this->lastName = $lastName;

            $this->addChangedProperties('society', $lastName);
        }

        return $this;
    }

    /**
     * @var string
     */
    private $society;

    /**
     * @return string
     */
    public function getSociety()
    {
        return $this->society;
    }

    /**
     * @param string $society
     *
     * @return $this|self
     */
    public function setSociety(string $society = null) : self
    {
        if ($this->society != $society) {
            $this->society = $society;

            $this->addChangedProperties('society', $society);
        }

        return $this;
    }

    /**
     * @var GeocoderResult
     */
    private $address;

    /**
     * @return GeocoderResult
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param GeocoderResult $address
     *
     * @return $this|self
     */
    public function setAddress(GeocoderResult $address) : self
    {
        if ($this->address != $address) {
            $this->address = $address;

            $this->addChangedProperties('address', $address->getFormattedAddress());
        }

        return $this;
    }

    //region endregion

    //region Db Read

    /**
     * @param int $id
     *
     * @return $this|self|null
     */
    public static function getById(int $id)
    {
        $db = self::db(self::class);

        $sql = 'SELECT *
                FROM tbl_users_address
                WHERE address_id = :id';
        if ($result = $db->fetchOne($sql, ['id' => $id])) {
            $return = new static();
            $return->map($result);

            return $return;
        }

        return null;
    }

    /**
     * @param User $user
     *
     * @return CollectionModel|$this[]
     */
    public static function getByUser(User $user) : CollectionModel
    {
        $return = [];

        $db = self::db(self::class);
        $sql = 'SELECT *
                FROM tbl_users_address
                WHERE address_user_id = :userId
                AND address_deleted IS NULL';
        foreach ($db->query($sql, ['userId' => $user->getId()]) as $result) {
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
        $this->id = $result['address_id'];
        $this->userId = $result['address_user_id'];
        $this->main = (bool) $result['address_main'];
        $this->firstName = $result['address_firstname'];
        $this->lastName = $result['address_lastname'];
        $this->society = $result['address_society'];
        $this->address = new GeocoderResult();
        $this->address->jsonUnserialize($result['address_address']);
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

        $this->user = $user;
        $this->userId = $user->getId();

        $sql = 'INSERT INTO tbl_users_address
                SET address_user_id = :userId,
                    address_main = :main,
                    address_firstname = :firstName,
                    address_lastname = :lastName,
                    address_society = :society,
                    address_address = :address';

        $result = $db->query($sql, [
            'userId' => $this->userId,
            'main' => $this->main,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'society' => $this->society,
            'address' => self::encodeData($this->address),
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

        $sql = 'UPDATE tbl_users_address
                SET address_main = :main,
                    address_firstname = :firstName,
                    address_lastname = :lastName,
                    address_society = :society,
                    address_address = :address
                WHERE address_id = :id';

        $db->query($sql, [
            'id' => $this->id,
            'main' => $this->main,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'society' => $this->society,
            'address' => self::encodeData($this->address),
        ]);

        $this->changedProperties['operation'] = Change::OPERATION_UPDATE;
        $this->changedProperties['id'] = $this->id;

        return true;
    }

    /**
     * @param int $userId
     *
     * @return bool
     */
    public function delete(int $userId = null) : bool
    {
        $db = self::db(self::class);

        $sql = 'UPDATE tbl_users_address
                        SET address_deleted = :deleted
                        WHERE address_id = :id';

        $db->query($sql, [
            'id' => $this->id,
            'deleted' => new DateTime(),
        ]);

        $this->changedProperties['operation'] = Change::OPERATION_DELETE;
        $this->changedProperties['id'] = $this->id;

        return true;
    }

    //endregion
}
