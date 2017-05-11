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

use Cawa\App\AbstractApp;
use Cawa\Core\DI;
use Cawa\Date\DateTime;
use Cawa\Db\DatabaseFactory;
use Cawa\Http\File;
use Cawa\HttpClient\Adapter\AbstractClient;
use Cawa\HttpClient\HttpClient;
use Cawa\Net\Uri;
use Cawa\Orm\CollectionModel;
use Cawa\Orm\Model;
use Cawa\Renderer\AssetTrait;
use Cawa\Renderer\HtmlElement;

class Upload extends Model
{
    use DatabaseFactory;
    use AssetTrait;

    //region Constants

    const MODEL_TYPE = 'UPLOAD';

    const PROVIDER_FS = 'FS';
    const PROVIDER_DB = 'DB';

    const KEY_IMAGE = 'IMAGE';
    const KEY_LOGO = 'LOGO';

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
        if ($type !== $this->type) {
            $this->type = $type;

            $this->addChangedProperties('type', $type);
        }

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
        if ($externalId !== $this->externalId) {
            $this->externalId = $externalId;

            $this->addChangedProperties('externalId', $externalId);
        }

        return $this;
    }

    /**
     * @var string
     */
    private $provider;

    /**
     * @return string
     */
    public function getProvider() : string
    {
        return $this->provider;
    }

    /**
     * @param string $provider
     *
     * @return $this|self
     */
    public function setProvider(string $provider) : self
    {
        if ($provider !== $this->provider) {
            $this->provider = $provider;

            $this->addChangedProperties('provider', $provider);
        }

        return $this;
    }

    /**
     * @var string
     */
    private $key;

    /**
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }

    /**
     * @param string $key
     *
     * @return $this|self
     */
    public function setKey(string $key) : self
    {
        if ($key !== $this->key) {
            $this->key = $key;

            $this->addChangedProperties('key', $key);
        }

        return $this;
    }

    /**
     * @var string
     */
    private $path;

    /**
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return $this|self
     */
    public function setPath(string $path) : self
    {
        if ($path !== $this->path) {
            $this->path = $path;

            $this->addChangedProperties('path', $path);
        }

        return $this;
    }

    /**
     * @var string
     */
    private $name;

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this|self
     */
    public function setName(string $name) : self
    {
        if ($name !== $this->name) {
            $this->name = $name;

            $this->addChangedProperties('name', $name);
        }

        return $this;
    }

    /**
     * @var string
     */
    private $extension;

    /**
     * @return string
     */
    public function getExtension() : string
    {
        return $this->extension;
    }

    /**
     * @param string $extension
     *
     * @return $this|self
     */
    public function setExtension(string $extension) : self
    {
        if ($extension !== $this->extension) {
            $this->extension = strtolower($extension);

            $this->addChangedProperties('extension', $extension);
        }

        return $this;
    }

    /**
     * @var string
     */
    private $contentType;

    /**
     * @return string
     */
    public function getContentType() : string
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     *
     * @return $this|self
     */
    public function setContentType(string $contentType) : self
    {
        if ($contentType !== $this->contentType) {
            $this->contentType = $contentType;

            $this->addChangedProperties('contentType', $contentType);
        }

        return $this;
    }

    /**
     * @var int
     */
    private $size;

    /**
     * @return int
     */
    public function getSize() : int
    {
        return $this->size;
    }

    /**
     * @param int $size
     *
     * @return $this|self
     */
    public function setSize(int $size) : self
    {
        if ($size !== $this->size) {
            $this->size = $size;

            $this->addChangedProperties('size', $size);
        }

        return $this;
    }

    /**
     * @var int
     */
    private $order;

    /**
     * @return int
     */
    public function getOrder() : int
    {
        return $this->order;
    }

    /**
     * @param int $order
     *
     * @return $this|self
     */
    public function setOrder(int $order) : self
    {
        if ($order !== $this->order) {
            $this->order = $order;

            $this->addChangedProperties('order', $order);
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

    private $content;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     *
     * @return Upload
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    //endregion

    //region Logic

    /**
     * @param File $file
     */
    public function setFromFile(File $file)
    {
        $pathInfo = pathinfo($file->getName());
        $this->setContentType($file->getType());
        $this->setPath('/' . date('Y/m/d'));
        $this->setName(substr($pathInfo['basename'], 0, -strlen($pathInfo['extension']) - 1));
        $this->setExtension($pathInfo['extension']);
        $this->setSize($file->getSize());
        $this->content = $file->getContent();
    }

    /**
     * @param string $file
     *
     * @return $this|self
     */
    public function setFromPath(string $file) : self
    {
        $pathInfo = pathinfo($file);

        $this->path = $pathInfo['dirname'];
        $this->name = $pathInfo['filename'];
        $this->extension = $pathInfo['extension'];

        return $this;
    }

    /**
     * @param Uri $uri
     *
     * @return $this|self
     */
    public function setFromUri(Uri $uri) : self
    {
        $url = $uri->get(false);
        $pathInfo = pathinfo($url);
        $this->setPath('/' . date('Y/m/d'));

        $response = (new HttpClient())
            ->setClientOption(AbstractClient::OPTIONS_FOLLOW_REDIRECTION, true)
            ->get($url);

        if ($response->getHeader('Content-Disposition')) {
            if (preg_match('`filename="([^"]+)"`', $response->getHeader('Content-Disposition'), $match)) {
                $pathInfo = pathinfo($match[1]);
            }
        }

        if (isset($pathInfo['extension'])) {
            $this->setName(substr($pathInfo['basename'], 0, -strlen($pathInfo['extension']) - 1));
            $this->setExtension($pathInfo['extension']);
        } else {
            $this->setName('file');
            $this->setExtension('jpg');
        }

        $this->setContentType($response->getHeader('Content-Type'));
        $this->setSize(strlen($response->getBody()));
        $this->content = $response->getBody();

        return $this;
    }

    /**
     * @param string $effects
     *
     * @return Uri
     */
    public function getUrl(string $effects = null) : Uri
    {
        if (!$this->id) {
            // fake upload
            $path = $this->asset($this->path . '/' . $this->name . '.' . $this->extension);
            $pathInfo = pathinfo($path->getPath());

            $uri = $pathInfo['dirname'] . '/' .
                $pathInfo['filename'] . '.' .
                ($effects ? 'imm:' . $effects . '.' : '') .
                $pathInfo['extension'];
        } else {
            $uri = DI::config()->get('upload/url') .
                $this->path . '/' . $this->id . '.' .
                $this->date->getTimestamp() . '.' .
                ($effects ? 'imm:' . $effects . '.' : '') .
                $this->extension;
        }

        return (new Uri(null, [Uri::OPTIONS_RELATIVE => false]))
            ->setPath($uri)
            ->setQueries([])
            ->setFragment()
        ;
    }

    /**
     * @param int|null $width
     * @param int|null $height
     *
     * @return HtmlElement
     */
    public function getImage(int $width = null, int $height = null) : HtmlElement
    {
        $element = (new HtmlElement('<img />'))
            ->addAttribute('src', $this->getUrl('fit[' . $width . ',' . $height . ']')->get());

        if ($width) {
            $element->addAttribute('width', (string) $width);
        }

        if ($height) {
            $element->addAttribute('height', (string) $height);
        }

        return $element;
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
                FROM tbl_commons_upload
                WHERE upload_id = :id 
                    AND upload_deleted IS NULL';
        if ($result = $db->fetchOne($sql, ['id' => $id])) {
            $return = new static();
            $return->map($result);

            return $return;
        }

        return null;
    }

    /**
     * @param string $type
     * @param int $id
     *
     * @return CollectionModel|$this[]
     */
    public static function getByExternalId(string $type, int $id) : CollectionModel
    {
        $return = [];
        $db = self::db(self::class);

        $sql = 'SELECT 
                    upload_id,
                    upload_type,
                    upload_external_id,
                    upload_provider,
                    upload_key,
                    upload_path,
                    upload_name,
                    upload_extension,
                    upload_content_type,
                    upload_size,
                    upload_order,
                    upload_date,
                    NULL AS upload_content,
                    upload_deleted 
                FROM tbl_commons_upload
                WHERE upload_type = :type 
                    AND upload_external_id = :id 
                    AND upload_deleted IS NULL
                ORDER BY upload_order';

        foreach ($db->query($sql, ['type' => $type, 'id' => $id]) as $result) {
            $item = new static();
            $item->map($result);
            $return[] = $item;
        }

        $collection = new CollectionModel($return);

        return $collection;
    }

    /**
     * @param string $type
     * @param int $id
     *
     * @return int
     */
    private static function getCurrentOrder(string $type, int $id) : int
    {
        $db = self::db(self::class);

        $sql = 'SELECT MAX(upload_order) as max_order
                FROM tbl_commons_upload
                WHERE upload_type = :type 
                    AND upload_external_id = :id 
                    AND upload_deleted IS NULL';
        $row = $db->fetchOne($sql, ['type' => $type, 'id' => $id]);

        return $row['max_order'] ? (int) $row['max_order'] : 1;
    }

    //endregion

    /**
     * {@inheritdoc}
     */
    public function map(array $result)
    {
        $this->id = $result['upload_id'];
        $this->externalId = $result['upload_external_id'];
        $this->type = $result['upload_type'];
        $this->provider = $result['upload_provider'];
        $this->key = $result['upload_key'];
        $this->path = $result['upload_path'];
        $this->name = $result['upload_name'];
        $this->extension = $result['upload_extension'];
        $this->contentType = $result['upload_content_type'];
        $this->size = $result['upload_size'];
        $this->order = $result['upload_order'];
        $this->content = $result['upload_content'] ? base64_decode($result['upload_content']) : null;
        $this->date = $result['upload_date'];
    }

    //endregion

    //region Db Alter

    public function duplicate() : self
    {
        $item = self::getById($this->id);
        $item->changedProperties['duplicateFrom'] = $item->id;
        $item->id = null;
        $item->externalId = null;

        return $item;
    }

    /**
     * @param int|null $externalId
     *
     * @return bool
     */
    public function insert(int $externalId = null) : bool
    {
        return $this->save($externalId);
    }

    /**
     * @param int|null $externalId
     *
     * @return bool
     */
    public function update(int $externalId = null) : bool
    {
        return $this->save($externalId);
    }

    /**
     * @param int $externalId
     *
     * @return bool
     */
    private function save(int $externalId = null) : bool
    {
        if ($externalId) {
            $this->setExternalId($externalId);
        }

        if (!$this->hasChangedProperties()) {
            return true;
        }

        $db = self::db(self::class);

        $this->provider = self::PROVIDER_DB;
        $this->date = new DateTime();

        if (!$this->order) {
            $this->order = self::getCurrentOrder($this->type, $this->externalId);
        }

        $sql = 'SET upload_external_id = :externalId,
                    upload_type = :type,
                    upload_provider = :provider,
                    upload_key = :key,
                    upload_path = :path,
                    upload_name = :name,
                    upload_extension = :extension,
                    upload_content_type = :contentType,
                    upload_size = :size,
                    upload_order = :order,
                    upload_date = :date';

        if ($this->content) {
            $sql .= ', upload_content = :content';
        }

        if ($this->id) {
            $sql = "UPDATE tbl_commons_upload\n" . $sql . "\nWHERE upload_id = :id";
            $this->changedProperties['operation'] = Change::OPERATION_UPDATE;
        } else {
            $sql = "INSERT INTO tbl_commons_upload\n" . $sql;
            $this->changedProperties['operation'] = Change::OPERATION_INSERT;
        }

        $params = [
            'externalId' => $this->externalId,
            'type' => $this->type,
            'provider' => $this->provider,
            'key' => $this->key,
            'path' => $this->path,
            'name' => $this->name,
            'extension' => $this->extension,
            'contentType' => $this->contentType,
            'size' => $this->size,
            'date' => $this->date,
            'order' => $this->order,
        ];

        if ($this->content) {
            $params['content'] = $this->provider == self::PROVIDER_DB ? base64_encode($this->content) : null;
        }

        if ($this->id) {
            $params['id'] = $this->id;
        }

        $result = $db->query($sql, $params);

        if (!$this->id) {
            $this->id = $result->insertedId();
        }

        $this->changedProperties['id'] = $this->id;

        if ($this->content && $this->provider == self::PROVIDER_FS) {
            // @TODO: remove old item on update
            $savePath = AbstractApp::getAppRoot() . DI::config()->get('upload/path') . $this->path . '/';
            if (!file_exists($savePath)) {
                mkdir($savePath, 0777, true);
            }

            file_put_contents($savePath . $this->id . '.' . $this->extension, $this->content);
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

        $sql = 'UPDATE tbl_commons_upload
                    SET upload_deleted = :deleted
                    WHERE upload_id = :id';

        $db->query($sql, [
            'id' => $this->id,
            'deleted' => new DateTime(),
        ]);

        return true;
    }

    //endregion
}