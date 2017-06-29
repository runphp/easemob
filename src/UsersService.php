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

/**
 * @see http://docs.easemob.com/start/100serverintegration/20users#im_用户管理
 *
 * @author hehui<hehui@eelly.net>
 */
class UsersService extends AbstractService
{
    /**
     * 注册 IM 用户[单个].
     *
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $nickname 昵称
     *
     * @return array
     */
    public function createUser(string $username, string $password, string $nickname = ''): array
    {
        $body = [
            'username' => $username,
            'password' => $password,
            'nickname' => $nickname,
        ];

        return $this->getResult(self::METHOD_POST, '/users', $body);
    }

    /**
     * 注册 IM 用户[批量]
     * 批量注册的用户数量不要过多，建议在20-60之间.
     *
     * @param array  $users               用户列表
     * @param string $users[]['username'] 用户名
     * @param string $users[]['password'] 密码
     */
    public function createUsers(array $users)
    {
        return $this->getResult(self::METHOD_POST, '/users', $users);
    }

    /**
     * 获取单个用户.
     *
     * @param string $username
     *
     * @return array
     */
    public function getUser(string $username)
    {
        return $this->getResult(self::METHOD_GET, '/users/'.$username);
    }

    /**
     * 删除 IM 用户[单个].
     *
     * @param string $username
     *
     * @return array
     */
    public function deleteUser(string $username)
    {
        return $this->getResult(self::METHOD_DELETE, '/users/'.$username);
    }

    /**
     * 删除 IM 用户[批量].
     *
     * 该接口不安全而且有问题
     *
     * @param int $limit 指定删除数据的最大条数
     */
    public function deleteUsers(int $limit = 1)
    {
        return [];
        //return $this->getResult(self::METHOD_DELETE, '/users?limit='.$limit);
    }

    /**
     * 重置 IM 用户密码
     *
     *
     * @param string $username
     * @param string $newPassword
     *
     * @return array
     */
    public function updatePassword($username, $newPassword)
    {
        $body = [
            'newpassword' => $newPassword,
        ];

        return $this->getResult(self::METHOD_PUT, '/users/'.$username.'/password', $body);
    }

    /**
     * 给 IM 用户添加好友.
     *
     * @param string $ownerUsername
     * @param string $friendUsername
     *
     * @return array
     */
    public function addFriend(string $ownerUsername, string $friendUsername)
    {
        return $this->contactsUsers($ownerUsername, $friendUsername, self::METHOD_POST);
    }

    /**
     * 解除 IM 用户的好友关系
     * 从 IM 用户的好友列表中移除一个用户.
     *
     * @param string $ownerUsername
     * @param string $friendUsername
     *
     * @return array
     */
    public function deleteFriend(string $ownerUsername, string $friendUsername)
    {
        return $this->contactsUsers($ownerUsername, $friendUsername, self::METHOD_DELETE);
    }

    /**
     * 获取 IM 用户的好友列表.
     *
     * @param string $ownerUsername
     *
     * @return array
     */
    public function getFriends(string $ownerUsername)
    {
        return $this->getResult(self::METHOD_GET, '/users/'.$ownerUsername.'/contacts/users');
    }

    /**
     * 往 IM 用户的黑名单中加人.
     *
     * 使用示例：
     *
     * ```
     * // 加单个人到黑名单
     * $userService->addBlockUsers('xiaoming2', 'xiaoming3');
     * // 加多个人到黑名单
     * $userService->addBlockUsers('xiaoming2', ['xiaoming1','xiaoming3']);
     * ```
     *
     * @param string       $ownerUsername 用户名(要添加好友的用户名)
     * @param stirng|array $usernames     黑名单(被添加的用户名)
     *
     * @return array
     */
    public function addBlockUsers(string $ownerUsername, $usernames)
    {
        $body = [
            'usernames' => (array) $usernames,
        ];

        return $this->getResult(self::METHOD_POST, '/users/'.$ownerUsername.'/blocks/users', $body);
    }

    /**
     * 从 IM 用户的黑名单中减人
     * 从一个 IM 用户的黑名单中减人。将用户从黑名单移除后，恢复好友关系，可以互相收发消息.
     *
     * @param string $ownerUsername
     * @param string $blockedUsername
     *
     * @return array
     */
    public function deleteBlockedUser(string $ownerUsername, string $blockedUsername)
    {
        return $this->getResult(self::METHOD_DELETE, '/users/'.$ownerUsername.'/blocks/users');
    }

    /**
     * 获取 IM 用户的黑名单
     * 获取一个IM用户的黑名单。黑名单中的用户无法给该 IM 用户发送消息。
     *
     *
     * @param string $username
     *
     * @return array
     */
    public function getBlockedUsers(string $username)
    {
        $service = 'users/'.$username.'/blocks/users';

        return $this->getResult(self::METHOD_GET, '/users/'.$username.'/blocks/users');
    }

    /**
     * 获取用户token.
     *
     *
     * @param string $username
     * @param string $password
     *
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    public function getUserToken(string $username, string $password)
    {
        $body = [
            'username' => $username,
            'password' => $password,
        ];

        return $this->provider->getAccessToken('password', $body);
    }

    /**
     * 修改用户昵称.
     *
     *
     * @param string $username
     * @param string $nickname
     *
     * @return array
     */
    public function updateNickname(string $username, string $nickname)
    {
        $body = [
            'nickname' => $nickname,
        ];

        return $this->getResult(self::METHOD_PUT, '/users/'.$username, $body);
    }

    /**
     * 查看用户在线状态
     *
     *
     * @param string $username
     *
     * @return array
     */
    public function status(string $username)
    {
        return $this->getResult(self::METHOD_GET, '/users/'.$username.'/status');
    }

    /**
     * 给 IM 用户添加好友
     * 或
     * 解除 IM 用户的好友关系.
     *
     * @param string $ownerUsername  用户名(要添加好友的用户名)
     * @param string $friendUsername 用户名(被添加的用户名)
     * @param string $method         post 添加 delete 解除
     *
     * @return array
     */
    private function contactsUsers(string $ownerUsername, string $friendUsername, string $method)
    {
        return $this->getResult($method, '/users/'.$ownerUsername.'/contacts/users/'.$friendUsername);
    }
}
