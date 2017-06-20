<?php

declare(strict_types=1);
/*
 * PHP version 7.1
 *
 * @copyright Copyright (c) 2012-2017 EELLY Inc. (https://www.eelly.com)
 * @link      https://api.eelly.com
 * @license   衣联网版权所有
 */

namespace Eelly\Easemob;

use GuzzleHttp\Client as HttpClient;
use League\Flysystem\AdapterInterface as FlysystemInterface;
use Phalcon\Cache\BackendInterface as CacheInterface;

/**
 * @author hehui<hehui@eelly.net>
 */
class Manager
{
    private $options;

    private $uri;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var FlysystemInterface
     */
    private $flysystem;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @param array     $options   配置信息
     * @param Flysystem $flysystem 文件存储接口
     * @param Cache     $cache     缓存接口
     */
    public function __construct(array $options, ?FlysystemInterface $flysystem, ?CacheInterface $cache)
    {
        $this->options = $options;
        $this->uri = 'https://a1.easemob.com/'.$options['orgName'].'/'.$options['appName'].'/';
        $this->client = new HttpClient();
        $this->flysystem = $flysystem;
        $this->cache = $cache;
    }

    /**
     * 获取授权管理员 token.
     */
    public function token(): string
    {
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }
}
