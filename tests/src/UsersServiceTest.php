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

use Eelly\Easemob\Exception\UsersException;
use Eelly\OAuth2\Client\Provider\EasemobProvider;
use PHPUnit\Framework\TestCase;

/**
 * @author hehui<hehui@eelly.net>
 */
class UsersServiceTest extends TestCase
{
    /**
     * @var UsersService
     */
    private $usersService;

    public function setUp(): void
    {
        $options = [
            'clientId' => 'YXA6UR5jYHMdEeWVfi1kLYliWw',
            'clientSecret' => 'YXA61KlUhrvYXNTT_aymCx0bPDfoQMs',
            'orgName' => 'www-eelly-com',
            'appName' => 'buyerdevelopment',
            'signResponse' => 'syn32i94453c7a5', // 输出签名
            'signRequest' => 'knbxouvb0x0xrdc',  // 输入签名
        ];
        $provider = new EasemobProvider($options);
        $this->usersService = new UsersService($provider);
    }

    public function testCreateUser(): void
    {
        $return = $this->usersService->createUser('time_'.time(), '123456');
        $this->assertInternalType('array', $return);
        $this->expectException(UsersException::class);
        $this->usersService->createUser('time_123', '123456');
        $this->usersService->createUser('time_123', '123456');
    }
}
