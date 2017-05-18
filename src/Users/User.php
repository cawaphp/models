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

use Cawa\App\HttpFactory;
use Cawa\Core\DI;
use Cawa\Date\Date;
use Cawa\Date\DateTime;
use Cawa\Db\DatabaseFactory;
use Cawa\Events\DispatcherFactory;
use Cawa\Http\Cookie;
use Cawa\Http\File;
use Cawa\Intl\Exceptions\Duplicate;
use Cawa\Intl\Exceptions\Invalid;
use Cawa\Intl\PhoneNumber;
use Cawa\Intl\TranslatorFactory;
use Cawa\Models\Commons\Event;
use Cawa\Models\Commons\Upload;
use Cawa\Net\Ip;
use Cawa\Orm\CollectionModel;
use Cawa\Orm\Model;
use Cawa\Session\Orm\SessionSleepTrait;
use Cawa\Session\Orm\SessionTrait;

class User extends Model
{
    use HttpFactory;
    use DatabaseFactory;
    use DispatcherFactory;
    use SessionTrait;
    use SessionSleepTrait;
    use TranslatorFactory;

    //region Constant

    const MODEL_TYPE = 'USER';

    /**
     * Gender Male
     */
    const GENDER_MALE = 'M';

    /**
     * Gender Female
     */
    const GENDER_FEMALE = 'F';

    /**
     *  Token cookie name
     */
    const COOKIE_TOKEN = 'TKN';

    //endregion

    //region Mutator

    /**
     * @var int
     */
    protected $id;

    /**
     * @return int
     */
    public function getId() : ?int
    {
        return $this->id;
    }

    /**
     * @var string
     */
    protected $email;

    /**
     * @return string
     */
    public function getEmail() : ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     *
     * @return $this|self
     */
    public function setEmail(string $email) : self
    {
        if ($this->email !== $email) {

            $this->controlEmail($email);

            $this->email = strtolower($email);

            $this->addChangedProperties('email', $this->email);
        }

        return $this;
    }

    /**
     * @var string
     */
    protected $phone;

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @throws Invalid
     *
     * @return $this|User
     */
    public function setPhone(string $phone = null) : self
    {
        if ($this->phone !== $phone) {
            $detail = new PhoneNumber($phone);
            if (!$detail->isValid()) {
                throw new Invalid('global.user/errors/invalidPhone');
            }

            $this->phone = $phone;

            $this->addChangedProperties('phone', $phone);
        }

        return $this;
    }

    /**
     * @var string
     */
    protected $firstName;

    /**
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     *
     * @return $this|self
     */
    public function setFirstName(string $firstName = null) : self
    {
        if ($firstName !== $this->firstName) {
            $this->firstName = $firstName;

            $this->addChangedProperties('firstName', $firstName);
        }

        return $this;
    }

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @return string|null
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     *
     * @return $this|self
     */
    public function setLastName(string $lastName = null) : self
    {
        if ($this->lastName !== $lastName) {
            $this->lastName = $lastName;

            $this->addChangedProperties('lastName', $lastName);
        }

        return $this;
    }

    /**
     * @var string
     */
    protected $gender;

    /**
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     *
     * @return $this|self
     */
    public function setGender($gender) : self
    {
        if ($this->gender !== $gender) {
            $this->gender = $gender;

            $this->addChangedProperties('gender', $gender);
        }

        return $this;
    }

    /**
     * @var string
     */
    protected $timezone;

    /**
     * @return string|null
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     *
     * @return $this|self
     */
    public function setTimezone(string $timezone) : self
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * @var Date
     */
    protected $birthday;

    /**
     * @return Date
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * @param Date $birthday
     *
     * @return $this|self
     */
    public function setBirthday(Date $birthday = null) : self
    {
        if ($this->birthday != $birthday) {
            $this->birthday = $birthday;

            $this->addChangedProperties('birthday', $birthday);
        }

        return $this;
    }

    /**
     * @var DateTime
     */
    protected $loginDate;

    /**
     * @return DateTime
     */
    public function getLoginDate()
    {
        return $this->loginDate;
    }

    /**
     * @var bool
     */
    protected $logged = false;

    /**
     * @return bool
     */
    public function isLogged() : bool
    {
        return $this->logged;
    }

    /**
     * @var bool
     */
    protected $signed = false;

    /**
     * @return bool
     */
    public function isSigned() : bool
    {
        return $this->signed;
    }

    /**
     * @var CollectionModel|Auth[]
     */
    protected $auths;

    /**
     * @return CollectionModel|Auth[]
     */
    public function getAuths() : CollectionModel
    {
        if (!$this->auths) {
            if (!$this->id) {
                $this->auths = new CollectionModel();
            } else {
                $this->auths = Auth::getByUserId($this->id);
            }
        }

        return $this->auths ;
    }

    /**
     * @var CollectionModel|Status[]
     */
    protected $status;

    /**
     * @return CollectionModel|Status[]
     */
    public function getStatus() : CollectionModel
    {
        if (!$this->status) {
            if (!$this->id) {
                $this->status = new CollectionModel();
            } else {
                $this->status = Status::getByUserId($this->id);
            }
        }

        return $this->status;
    }

    /**
     * @var CollectionModel|Upload[]
     */
    protected $upload;

    /**
     * @return CollectionModel|Upload[]
     */
    protected function getUpload() : CollectionModel
    {
        if (!$this->upload) {
            $this->upload = Upload::getByExternalId(self::MODEL_TYPE, $this->id);
        }

        return $this->upload;
    }

    /**
     * @return Upload|null
     */
    public function getAvatar()
    {
        return $this->getUpload()->findOne('getKey', Upload::KEY_IMAGE);
    }

    /**
     * @param File $avatar
     *
     * @return $this|self
     */
    public function setAvatar(File $avatar = null) : self
    {
        if ($avatar) {
            /** @var Upload $upload */
            $upload = $this->getUpload()->find('getKey', Upload::KEY_IMAGE)[0];
            if (!$upload) {
                $upload = new Upload();
                $upload->setType(self::MODEL_TYPE);
                $upload->setExternalId($this->id);
                $upload->setKey(Upload::KEY_IMAGE);
                $this->getUpload()->add($upload);
            }

            $upload->setFromFile($avatar);
        }

        return $this;
    }

    /**
     * @var CollectionModel|Address[]
     */
    protected $addresses;

    /**
     * @return CollectionModel|Address[]
     */
    public function getAddresses() : CollectionModel
    {
        if (!$this->addresses) {
            $this->addresses = Address::getByUser($this);
        }

        return $this->addresses;
    }

    //endregion

    //region Session

    /**
     * {@inheritdoc}
     */
    protected function sessionSleep() : array
    {
        return [
            'id' => $this->id,
            'logged' => $this->logged,
            'signed' => $this->signed,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected static function sessionWakeup(array $data)
    {
        $user = self::getById($data['id']);
        if (!$user) {
            return null;
        }

        $user->logged = $data['logged'];
        $user->signed = $data['logged'];

        return $user;
    }

    //endregion

    //region Logic

    /**
     * @param string $password
     * @param string $salt
     *
     * @return string
     */
    private static function encodePassword(string $password, string $salt) : string
    {
        return hash('sha256', $password . $salt);
    }

    /**
     * @param string $password
     * @param string $referer
     *
     * @return bool
     */
    public function signUpWithPassword(string $password, string $referer) : bool
    {
        $db = self::db(self::class);
        $started = $db->startTransactionIf();

        $this->insert();

        $salt = md5(uniqid((string) random_bytes(128), true));

        $auth = new Auth();
        $auth->setType(Auth::TYPE_PASSWORD)
            ->setUid((string) $this->getId())
            ->setData([
                'salt' => $salt,
                'password' => self::encodePassword($password, $salt),
            ]);

        $this->getAuths()->add($auth);

        $this->update();

        self::emit(new Event('user.register', $auth, null, [
            'referer' => $referer
        ]));

        $this->logged = true;
        $this->signed = true;

        if (!$started) {
            $db->commit();
        }

        return true;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    public function changePassword(string $password) : bool
    {
        $salt = md5(uniqid((string) random_bytes(128), true));

        $auth = $this->getAuths()->findOne('getType', Auth::TYPE_PASSWORD);

        if (!$auth) {
            $auth = new Auth();
            $auth->setType(Auth::TYPE_PASSWORD)
                ->setUid((string) $this->getId())
            ;

            $this->getAuths()->add($auth);
        }

        $auth->setData([
            'salt' => $salt,
            'password' => self::encodePassword($password, $salt),
        ]);

        $this->update();

        return true;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @throws Invalid
     *
     * @return $this|self
     */
    public static function loginPassword(string $email, string $password) : self
    {
        $db = self::db(self::class);
        $db->startTransaction();

        $return = self::getByEmailAndAuthType($email, Auth::TYPE_PASSWORD);

        if (!$return) {
            throw new Invalid('global.user/errors/emailUnknown');
        }

        /** @var $user User */
        /** @var $auth Auth */
        list($user,  $auth) = $return;

        if (self::encodePassword($password, $auth->getData()['salt']) != $auth->getData()['password']) {
            throw new Invalid('global.user/errors/invalidPassword');
        }

        $user->logged = true;
        $user->signed = true;

        $user->updateLoginDate($auth);
        $auth->updateLoginDate();

        $db->commit();

        return $user;
    }

    /**
     * @param \Cawa\Oauth\User $oauthUser
     *
     * @return $this|self
     */
    public static function loginSocial(\Cawa\Oauth\User $oauthUser) : self
    {
        $db = self::db(self::class);
        $db->startTransaction();

        $return = self::getBySocialAuth(strtoupper($oauthUser->getType()), $oauthUser->getUid());

        if ($return) {
            // oauth found
            /** @var $user User */
            /** @var $auth Auth */
            list($user,  $auth) = $return;

            if ($auth->getData() !== $oauthUser->getExtraData()) {
                $auth->setData($oauthUser->getExtraData());
                $auth->update();
            }

            $user->updateLoginDate($auth);
            $auth->updateLoginDate();
        } else {
            // oauth not found
            $user = self::getByEmail($oauthUser->getEmail());

            if (!$user) {
                $user = new static();
                $user->setEmail($oauthUser->getEmail())
                    ->setFirstName($oauthUser->getFirstName())
                    ->setLastName($oauthUser->getLastName())
                    ->setGender($oauthUser->getGender())
                    ->setBirthday($oauthUser->getBirthday())
                ;

                if ($oauthUser->isVerified()) {
                    $user->getStatus()->add(new Status(Status::VERIFIED));
                }
            }

            $auth = new Auth();
            $auth->setType(strtoupper($oauthUser->getType()))
                ->setUid($oauthUser->getUid())
                ->setData($oauthUser->getExtraData())
            ;

            if ($user->getId()) {
                $user->getAuths()->add($auth);
                $user->update();
            } else {
                $user->auths = new CollectionModel();
                $user->auths->add($auth);
                $user->insert();

                self::emit(new Event('user.register', $auth));
            }

            $user->updateLoginDate($auth, false);
        }

        // fill missing data if any
        if ($user->getStatus()->find('getStatus', Status::VERIFIED)->count() == 0 &&
            $oauthUser->isVerified()
        ) {
            $user->getStatus()->add(new Status(Status::VERIFIED));
        }

        if (!$user->getFirstName() && $oauthUser->getFirstName()) {
            $user->setFirstName($oauthUser->getFirstName());
        }

        if (!$user->getLastName() && $oauthUser->getLastName()) {
            $user->setLastName($oauthUser->getLastName());
        }

        if (!$user->getGender() && $oauthUser->getGender()) {
            $user->setGender($oauthUser->getGender());
        }

        if (!$user->getBirthday() && $oauthUser->getBirthday()) {
            $user->setBirthday($oauthUser->getBirthday());
        }

        $user->update();

        $db->commit();

        $user->logged = true;
        $user->signed = true;

        return $user;
    }

    /**
     * @see http://stackoverflow.com/a/17266448/1590168
     *
     * @return User|bool
     */
    public static function loggedWithTokenCookie()
    {
        $cookie = self::request()->getCookie(self::COOKIE_TOKEN);
        if (!$cookie) {
            return false;
        }

        $explode = explode('|', $cookie->getValue());
        if (sizeof($explode) !== 3) {
            return false;
        }

        list($token, $userId, $hmac) = $explode;
        $userId = (int) $userId;

        $return = self::getByAuthTypeAndId(Auth::TYPE_TOKEN, $userId);
        if (!$return) {
            return false;
        }

        /** @var $user User */
        /** @var $auth Auth */
        list($user,  $auth) = $return;

        $hmacCalc = hash_hmac('sha256', implode('|', [
            $token,
            $user->id,
        ]), DI::config()->get('models/user/cookieToken'));

        // timing attacks
        if (!hash_equals($hmacCalc, $hmac)) {
            return false;
        }

        $data = null;
        foreach ($auth->getData()['tokens'] as $currentToken => $tokenData) {
            if (hash_equals($currentToken, $token)) {
                $data = $tokenData;
            }
        }

        // expiration
        $date = new DateTime($data['added']);

        if ($date->add(new \DateInterval('P30D')) < new DateTime()) {
            return false;
        }

        $auth->updateLoginDate();

        $user->logged = true;

        return $user;
    }

    /**
     * Add a cookie with token for long session
     */
    public function addTokenCookie()
    {
        if (!$this->signed) {
            throw new \LogicException("Can't add token cookie on unsigned account");
        }

        /** @var Auth $auth */
        $auth = $this->getAuths()->findOne('getType', Auth::TYPE_TOKEN);

        if (!$auth) {
            $auth = new Auth();
            $auth->setType(Auth::TYPE_TOKEN)
                ->setUid((string) $this->id)
            ;
        }

        $token = $auth->generateToken();
        $data = $auth->getData();

        $data['tokens'][$token] = ['added' => new DateTime()];

        // remove old token
        if ($cookie = self::request()->getCookie(self::COOKIE_TOKEN)) {
            $explode = explode('|', $cookie->getValue());
            if ($explode[1] == $this->id) {
                unset($data['tokens'][$explode[0]]);
            }
        }

        $auth->setData($data);

        if ($auth->getId()) {
            $auth->update();
        } else {
            $auth->insert($this);
        }

        $cookieValue = $token . '|' . $this->id;
        $cookieValue .= '|' . hash_hmac('sha256', $cookieValue, DI::config()->get('models/user/cookieToken'));

        self::response()->setCookie(new Cookie(self::COOKIE_TOKEN, $cookieValue, (new DateTime())->addYear(1)));
    }

    /**
     * @return bool
     */
    public function removeTokenCookie() : bool
    {
        self::response()->clearCookie(new Cookie(self::COOKIE_TOKEN));

        return true;
    }

    //endregion

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
                FROM tbl_users_user
                WHERE user_id = :id';
        if ($result = $db->fetchOne($sql, ['id' => $id])) {
            $return = new static();
            $return->map($result);

            return $return;
        }

        return null;
    }

    /**
     * @param string $email
     *
     * @return $this|self|null
     */
    public static function getByEmail(string $email)
    {
        $db = self::db(self::class);

        $sql = 'SELECT *
                FROM tbl_users_user
                WHERE user_email = :email
                !forUpdate';

        $result = $db->fetchOne($sql, [
            'email' => $email,
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
     * @param string $type
     * @param int $userId
     *
     * @return array|null
     */
    private static function getByAuthTypeAndId(string $type, int $userId)
    {
        $db = self::db(self::class);

        $sql = 'SELECT *
                FROM tbl_users_auth
                INNER JOIN tbl_users_user ON auth_user_id = user_id
                    AND user_deleted IS NULL
                WHERE auth_type = :type
                    AND auth_user_id = :userId
                ';
        if ($result = $db->fetchOne($sql, ['type' => $type, 'userId' => $userId])) {
            $user = new static();
            $user->map($result);

            $auth = new Auth();
            $auth->map($result);

            return [$user, $auth];
        }

        return null;
    }

    /**
     * @param string $type
     * @param string $uid
     *
     * @return array|null
     */
    private static function getBySocialAuth(string $type, string $uid)
    {
        $db = self::db(self::class);

        $sql = 'SELECT *
                FROM tbl_users_auth
                INNER JOIN tbl_users_user ON auth_user_id = user_id
                    AND user_deleted IS NULL
                WHERE auth_type = :type
                    AND auth_uid = :uid
                ';
        if ($result = $db->fetchOne($sql, ['type' => $type, 'uid' => $uid])) {
            $user = new static();
            $user->map($result);

            $auth = new Auth();
            $auth->map($result);

            return [$user, $auth];
        }

        return null;
    }

    /**
     * @param string $email
     * @param string $authType
     *
     * @return array|null
     */
    private static function getByEmailAndAuthType(string $email, string $authType)
    {
        $db = self::db(self::class);

        $sql = 'SELECT *
                FROM tbl_users_user
                INNER JOIN tbl_users_auth ON auth_user_id = user_id
                    AND auth_type = :type
                WHERE user_deleted IS NULL
                    AND user_email = :email
                ';
        if ($result = $db->fetchOne($sql, ['type' => $authType, 'email' => $email])) {
            $user = new static();
            $user->map($result);

            $auth = new Auth();
            $auth->map($result);

            return [$user, $auth];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function map(array $result)
    {
        $this->id = $result['user_id'];
        $this->email = $result['user_email'];
        $this->phone = $result['user_phone'];
        $this->firstName = $result['user_firstname'];
        $this->lastName = $result['user_lastname'];
        $this->gender = $result['user_gender'];
        $this->timezone = $result['user_timezone'];
        $this->birthday = $result['user_birthday'];
        $this->loginDate = $result['user_login_date'];
    }

    //endregion

    //region Db Alter

    /**
     * @return void
     */
    protected function saveDependencies()
    {
        if ($this->status) {
            $this->status->save($this)->apply([$this, 'importChangedProperties']);
        }

        if ($this->auths) {
            $this->auths->save($this)->apply([$this, 'importChangedProperties']);
        }

        if ($this->addresses) {
            $this->addresses->save($this)->apply([$this, 'importChangedProperties']);
        }

        if ($this->upload) {
            $this->upload->save($this->id)->apply([$this, 'importChangedProperties']);
        }
    }

    /**
     * @param bool $insert
     *
     * @return array
     */
    protected function saveQuery(bool $insert) : array
    {
        return [
            [
                'user_email = :email',
                'user_phone = :phone',
                'user_firstname = :firstName',
                'user_lastname = :lastName',
                'user_gender = :gender',
                'user_birthday = :birthday',
            ],
            [
                'email' => $this->email,
                'phone' => $this->phone,
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'gender' => $this->gender,
                'birthday' => $this->birthday,
            ]
        ];
    }

    /**
     * @param string $email
     *
     * @throws Duplicate
     */
    private function controlEmail(string $email)
    {
        $db = self::db(self::class);

        if ($user = self::getByEmail($email)) {
            if ($db->isTransactionStarted()) {
                $db->rollback();
            }

            if ($user->getAuths()->findOne('getType', Auth::TYPE_PASSWORD)) {
                throw new Duplicate('global.user/errors/emailDuplicate');
            } else {
                $auths = $user->getAuths()->findDifferent('getType', Auth::TYPE_TOKEN)->sort(function(Auth $a, Auth $b)
                {
                    return $a->getLoginDate()->getTimestamp() > $b->getLoginDate()->getTimestamp() ? -1 :  1;
                });

                if ($auths->count() == 0) {
                    throw new Duplicate('global.user/errors/emailDuplicate');
                } else {
                    throw new Duplicate('global.user/errors/emailDuplicateSocial', [
                        '%provider%' => self::trans('global.user/socials/' . strtolower($auths->first()->getType()))
                    ]);
                }
            }
        }
    }

    /**
     * @param int $userId
     *
     * @throws Duplicate
     *
     * @return bool
     */
    public function insert(int $userId = null) : bool
    {
        $db = self::db(self::class);

        $started = $db->startTransactionIf();
        $this->controlEmail($this->email);

        list($cols, $data) = $this->saveQuery(true);

        $sql = 'INSERT INTO tbl_users_user
                SET ' . implode(",\n", $cols);

        $result = $db->query($sql, $data);

        $this->id = $result->insertedId();

        $this->saveDependencies();

        $db->instanceDispatcher()->once('db.commit', function () use ($userId) {
            self::emit(new Event('model.insert', $this, $userId));
        });

        if (!$started) {
            $db->commit();
        }

        return true;
    }

    /**
     * @param int $userId
     *
     * @throws Duplicate
     *
     * @return bool
     */
    public function update(int $userId = null) : bool
    {
        $db = self::db(self::class);

        $started = $db->startTransactionIf();

        $user = self::getByEmail($this->email);
        if ($user && $user->getId() != $this->id) {
            $db->rollback();
            throw new Duplicate('global.user/errors/emailAlreadyUse');
        }

        if ($this->hasChangedProperties()) {
            $db = self::db(self::class);

            list($cols, $data) = $this->saveQuery(false);

            $sql = 'UPDATE tbl_users_user
                    SET ' . implode(",\n", $cols) . '
                    WHERE user_id = :id';

            $db->query($sql, array_merge($data, [
                'id' => $this->id
            ]));
        }

        $this->saveDependencies();

        if ($this->hasChangedProperties()) {
            $db->instanceDispatcher()->once('db.commit', function () use ($userId) {
                self::emit(new Event('model.update', $this, $userId));
            });
        }

        if (!$started) {
            $db->commit();
        }

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

        $sql = 'UPDATE tbl_users_user
                        SET user_deleted = :deleted
                        WHERE user_id = :id';

        $db->query($sql, [
            'id' => $this->id,
            'deleted' => new DateTime(),
        ]);

        self::emit(new Event('model.delete', $this, $userId));

        return true;
    }

    /**
     * @param Auth $auth
     * @param bool $updateUser if false only log login date with Models\Users\Login
     */
    private function updateLoginDate(Auth $auth, bool $updateUser = true)
    {
        if ($updateUser) {
            $db = self::db(self::class);

            $sql = 'UPDATE tbl_users_user
                    SET user_login_date = :date
                    WHERE user_id = :id';

            $db->query($sql, [
                'id' => $this->id,
                'date' => new DateTime()
            ]);
        }

        $login = new Login();
        $login->setUserId($this->id)
            ->setAuthId($auth->getId())
            ->setIp(Ip::get())
            ->insert();
    }

    //endregion
}
