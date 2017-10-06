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

namespace Cawa\Models\Commons\UploadProviders;

use Cawa\Db\DatabaseFactory;

class Database extends AbstractProvider
{
    use DatabaseFactory;

    /**
     * {@inheritdoc}
     */
    protected function getProviderContent() : string
    {
        $db = self::db(self::class);

        $sql = 'SELECT upload_content
                FROM tbl_commons_upload
                WHERE upload_id = :id';

        return base64_decode($db->fetchOne($sql, ['id' => $this->id])['upload_content']);
    }

    /**
     * {@inheritdoc}
     */
    protected function saveProviderContent(string $content) : bool
    {
        $db = self::db(self::class);

        $sql = 'UPDATE tbl_commons_upload
                SET upload_content = :content
                WHERE upload_id = :id';

        $db->query($sql, [
            'id' => $this->id,
            'content' => base64_encode($content),
        ]);

        return true;
    }
}
