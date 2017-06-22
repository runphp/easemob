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
use Psr\Http\Message\ResponseInterface;

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
        $options = [
            'body' => json_encode($body),
        ];
        $uri = $this->provider->getBaseAuthorizationUrl().'/users';
        $request = $this->provider->getAuthenticatedRequest(self::METHOD_POST, $uri, $this->token, $options);
        $parsed = $this->getParsedResponse($request);

        return $parsed;
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if (!empty($data['error'])) {
            $error = $data['error'];
            throw new UsersException($error, $response->getStatusCode(), $data);
        }
    }
}
