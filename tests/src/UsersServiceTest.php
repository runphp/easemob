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

use Eelly\Easemob\Exception\ClientException;
use Eelly\OAuth2\Client\Provider\EasemobProvider;
use League\OAuth2\Client\Token\AccessToken;
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
        $username = 'time_'.microtime(true);
        $return = $this->usersService->createUser($username, '123456');
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($username);
        $this->expectException(ClientException::class);
        $this->usersService->createUser('time_123', '123456');
        $this->usersService->createUser('time_123', '123456');
        $this->usersService->deleteUser('time_123');
    }

    public function testCreateUsers()
    {
        $users = [];
        for ($i = 0; $i < 10; ++$i) {
            $users[] = ['username' => 'time_'.$i.'_'.microtime(true), 'password' => '123456'];
        }
        $return = $this->usersService->createUsers($users);
        $this->assertInternalType('array', $return);

        foreach ($users as $item) {
            $this->usersService->deleteUser($item['username']);
        }
    }

    public function testGetUser()
    {
        $username = 'time_'.microtime(true);
        $this->usersService->createUser($username, '123456');
        $return = $this->usersService->getUser($username);
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($username);
    }

    public function testDeleteUser()
    {
        $username = 'time_'.microtime(true);
        $this->usersService->createUser($username, '123456');
        $return = $this->usersService->deleteUser($username);
        $this->assertInternalType('array', $return);
    }

    public function testDeleteUsers()
    {
        $return = $this->usersService->deleteUsers(2);
        $return = $this->assertInternalType('array', $return);
    }

    public function testUpdatePassword()
    {
        $username = 'time_'.microtime(true);
        $this->usersService->createUser($username, '123456');
        $return = $this->usersService->updatePassword($username, '123654');
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($username);
    }

    public function testAddFriend()
    {
        $username = 'time_'.microtime(true);
        $this->usersService->createUser($username, '123456');
        $username1 = 'time_1'.microtime(true);
        $this->usersService->createUser($username1, '123456');
        $return = $this->usersService->addFriend($username, $username1);
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($username);
        $this->usersService->deleteUser($username1);
    }

    public function testDeleteFriend()
    {
        $username = 'time_'.microtime(true);
        $this->usersService->createUser($username, '123456');
        $username1 = 'time_1'.microtime(true);
        $this->usersService->createUser($username1, '123456');
        $this->usersService->addFriend($username, $username1);
        $return = $this->usersService->deleteFriend($username, $username1);
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($username);
        $this->usersService->deleteUser($username1);
    }

    public function testGetFriends()
    {
        $username = 'time_'.microtime(true);
        $this->usersService->createUser($username, '123456');
        $username1 = 'time_1'.microtime(true);
        $this->usersService->createUser($username1, '123456');
        $this->usersService->addFriend($username, $username1);
        $return = $this->usersService->getFriends($username);
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($username);
        $this->usersService->deleteUser($username1);
    }

    public function testAddBlockUsers()
    {
        $username = 'time_'.microtime(true);
        $this->usersService->createUser($username, '123456');
        $username1 = 'time_1'.microtime(true);
        $this->usersService->createUser($username1, '123456');
        $return = $this->usersService->addBlockUsers($username, $username1);
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($username);
        $this->usersService->deleteUser($username1);
    }

    public function testDeleteBlockedUser()
    {
        $username = 'time_'.microtime(true);
        $this->usersService->createUser($username, '123456');
        $username1 = 'time_1'.microtime(true);
        $this->usersService->createUser($username1, '123456');
        $this->usersService->addBlockUsers($username, $username1);
        $return = $this->usersService->deleteBlockedUser($username, $username1);
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($username);
        $this->usersService->deleteUser($username1);
    }

    public function testGetBlockedUsers()
    {
        $username = 'time_'.microtime(true);
        $this->usersService->createUser($username, '123456');
        $username1 = 'time_1'.microtime(true);
        $this->usersService->createUser($username1, '123456');
        $this->usersService->addBlockUsers($username, $username1);
        $return = $this->usersService->getBlockedUsers($username);
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($username);
        $this->usersService->deleteUser($username1);
    }

    public function testGetUserToken()
    {
        $this->usersService->updatePassword('time_123', '123456');
        $return = $this->usersService->getUserToken('time_123', '123456');
        $this->assertInstanceOf(AccessToken::class, $return);
    }

    public function testUpdateNickname()
    {
        $username = 'time_'.microtime(true);
        $this->usersService->createUser($username, '123456');
        $return = $this->usersService->updateNickname($username, $username.'nick');
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($username);
    }

    public function testStatus()
    {
        $username = 'time_'.microtime(true);
        $this->usersService->createUser($username, '123456');
        $return = $this->usersService->status($username);
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($username);
    }
}
