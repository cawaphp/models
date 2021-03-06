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

use Cawa\Core\DI;
use Cawa\HttpClient\HttpClientFactory;
use Cawa\Models\Commons\UploadProviders\Openstack\IdentityV2Service;
use Cawa\Models\Commons\UploadProviders\Openstack\IdentityV3Service;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Stream;
use OpenStack\Common\Transport\Utils;
use OpenStack\ObjectStore\v1\Models\Container;

class Openstack extends AbstractProvider
{
    use HttpClientFactory;

    /**
     * @var Container
     */
    private $container;

    /**
     * @return Container
     */
    private function getContainer() : Container
    {

        if (!$this->container) {
            $options = DI::config()->get('upload/providerOptions/OPENSTACK');
            $client = self::guzzle(self::class);

            if (isset($options['username'])) {
                // Identity v2
                $clientOptions = $client->getConfig() + [
                    'base_uri' => Utils::normalizeUrl($options['authUrl']),
                ];

                $options['identityService'] = IdentityV2Service::factory(new Client($clientOptions));
            } else {
                // Identity v3
                $clientOptions = $client->getConfig() + [
                    'base_uri' => Utils::normalizeUrl($options['authUrl']),
                ];

                $options['identityService'] = IdentityV3Service::factory(new Client($clientOptions));
            }

            $openstack = new \OpenStack\OpenStack($options);
            $service = $openstack->objectStoreV1();
            $this->container = $service->getContainer($options['containerName']);
        }

        return $this->container;
    }

    /**
     * @param string $prefix
     *
     * @return array
     */
    public function listRemote(string $prefix = null) : array
    {
        $return = [];
        /** @var \OpenStack\ObjectStore\v1\Models\Object $object */
        foreach ($this->getContainer()->listObjects([
            'prefix' => $prefix,
        ]) as $object) {
            $return[$object->name] = $object->contentLength;
        }

        return $return;
    }

    /**
     * @param string $path
     *
     * @return $this|self
     */
    public function setFromRemotePath(string $path) : self
    {
        $pathInfo = pathinfo($path);

        $this->path = $path;
        $this->name = $pathInfo['filename'];
        $this->extension = $pathInfo['extension'];

        /** @var \OpenStack\ObjectStore\v1\Models\Object $object */
        $object = $this->getContainer()->getObject(ltrim($this->path, '/'));
        $object->retrieve();

        $this->contentType = $object->contentType;
        $this->size = $object->contentLength;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function getProviderContent() : string
    {
        /** @var Stream $download */
        $download = $this->getContainer()->getObject(ltrim($this->path, '/'))->download();

        return $download->getContents();
    }

    /**
     * {@inheritdoc}
     */
    protected function saveProviderContent(string $content) : bool
    {
        if (!$this->path) {
            $this->setPath($this->getDefaultPath());
            $this->save();
        }

        $this->getContainer()->createObject([
            'name' => ltrim($this->path, '/'),
            'content' => $content,
        ]);

        return true;
    }
}
