<?php

declare(strict_types=1);
/*
 * This file is part of eelly package.
 *
 * (c) eelly.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
