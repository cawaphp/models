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
use Cawa\Events\DispatcherFactory;
use Cawa\Models\Commons\Event as ModelEvent;
use Cawa\Models\Properties\UserTrait;
use Cawa\Orm\Model;

class Comment extends Model
{
    use DatabaseFactory;
    use DispatcherFactory;
    use UserTrait;

    const MODEL_TYPE = 'COMMENT';

    //region Mutator

    /**
     * @var int
     */
    private $id;

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
    private $comment;

    /**
     * @return string
     */
    public function getComment() : ?string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return $this|self
     */
    public function setComment(string $comment) : self
    {
        if ($comment !== $this->comment) {
            $this->comment = $comment;

            $this->addChangedProperties('comment', $comment);
        }

        return $this;
    }

    /**
     * @var int
     */
    private $rating;

    /**
     * @return int
     */
    public function getRating() : ?int
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     *
     * @return $this|self
     */
    public function setRating(int $rating) : self
    {
        if ($rating !== $this->rating) {
            $this->rating = $rating;

            $this->addChangedProperties('rating', $rating);
        }

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
     * @var DateTime
     */
    private $deletedDate;

    /**
     * @return DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
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
                FROM tbl_commons_comment
                WHERE comment_id = :id';
        if ($result = $db->fetchOne($sql, ['id' => $id])) {
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
        $this->id = $result['comment_id'];
        $this->externalId = $result['comment_external_id'];
        $this->type = $result['comment_type'];
        $this->userId = $result['comment_user_id'];
        $this->comment = $result['comment_comment'];
        $this->rating = $result['comment_rating'];
        $this->date = $result['comment_date'];
        $this->deletedDate = $result['comment_deleted'];
    }

    //endregion

    //region Db Alter

    /**
     * @param bool $insert
     *
     * @return array
     */
    protected function saveQuery(bool $insert) : array
    {
        $cols = [
            'comment_comment = :comment',
            'comment_rating = :rating',
        ];

        $data = [
            'comment' => $this->comment,
            'rating' => $this->rating,
        ];

        if (!$insert) {
            return [$cols, $data];
        }

        return [
            array_merge($cols, [
                'comment_external_id = :externalId',
                'comment_type = :type',
                'comment_user_id = :userId',
                'comment_date = :date',
            ]),
            array_merge($data, [
                'externalId' => $this->externalId,
                'type' => $this->type,
                'userId' => $this->userId,
                'date' => $this->date,
            ]),
        ];
    }

    /**
     * @param int $userId
     *
     * @return bool
     */
    public function insert(int $userId = null) : bool
    {
        $db = self::db(self::class);

        $this->date = new DateTime();

        list($cols, $data) = $this->saveQuery(true);

        $sql = 'INSERT INTO tbl_commons_comment
                SET ' . implode(",\n", $cols);

        $result = $db->query($sql, $data);

        $this->id = $result->insertedId();

        self::emit(new ModelEvent('model.insert', $this, $userId));

        return true;
    }

    /**
     * @param int $userId
     *
     * @return bool
     */
    public function update(int $userId = null) : bool
    {
        $db = self::db(self::class);

        list($cols, $data) = $this->saveQuery(false);

        $sql = 'UPDATE tbl_commons_comment
                SET ' . implode(",\n", $cols) . '
                WHERE comment_id = :id';

        $db->query($sql, array_merge($data, [
            'id' => $this->id,
        ]));

        self::emit(new ModelEvent('model.update', $this, $userId ? $this->getUserId() : null));

        return true;
    }

    /**
     * @param int $userId
     *
     * @return bool
     */
    public function delete(int $userId = null)
    {
        $db = self::db(self::class);

        $sql = 'UPDATE tbl_commons_comment
                SET comment_deleted = NOW()
                WHERE comment_id = :id';

        $db->query($sql, [
            'id' => $this->id,
        ]);

        self::emit(new ModelEvent('model.delete', $this, $userId));

        return true;
    }

    //endregion
}
