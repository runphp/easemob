<?php
/*
 * This file is part of eelly package.
 *
 * (c) eelly.com
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eelly\Easemob;

use Eelly\OAuth2\Client\Provider\EasemobProvider;
use PHPUnit\Framework\TestCase;

class MessagesServiceTest extends TestCase
{
    /**
     * @var UsersService
     */
    private $usersService;

    /**
     * @var MessagesService
     */
    private $messagesService;

    /**
     * @var string
     */
    private $testFile;

    public function setUp(): void
    {
        $options = [
            'clientId'     => 'YXA6UR5jYHMdEeWVfi1kLYliWw',
            'clientSecret' => 'YXA61KlUhrvYXNTT_aymCx0bPDfoQMs',
            'orgName'      => 'www-eelly-com',
            'appName'      => 'buyerdevelopment',
            'signResponse' => 'syn32i94453c7a5', // 输出签名
            'signRequest'  => 'knbxouvb0x0xrdc',  // 输入签名
        ];
        $provider = new EasemobProvider($options);
        $this->usersService = new UsersService($provider);
        $this->messagesService = new MessagesService($provider);
        $this->testFile = dirname(__DIR__).'/resources/test.jpg';
    }

    public function testSendText()
    {
        $from = 'time_'.microtime(true);
        $to = 'time_1_'.microtime(true);
        $this->usersService->createUsers([
            ['username' => $from, 'password' => '123456'],
            ['username' => $to, 'password' => '123456'],
        ]);

        $return = $this->messagesService->sendText($from, $to, 'hello eelly!', ['name' => 'eelly']);
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($from);
        $this->usersService->deleteUser($to);
    }

    public function testSendPicture()
    {
        $from = 'time_'.microtime(true);
        $to = 'time_1_'.microtime(true);
        $this->usersService->createUsers([
            ['username' => $from, 'password' => '123456'],
            ['username' => $to, 'password' => '123456'],
        ]);

        $return = $this->messagesService->sendPicture($from, $to, $this->testFile, 'my picture', ['name' => 'eelly']);
        $this->assertInternalType('array', $return);
        $this->usersService->deleteUser($from);
        $this->usersService->deleteUser($to);
    }
}
