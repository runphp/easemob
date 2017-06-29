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

/**
 * 发送消息.
 *
 * @author hehui<hehui@eelly.net>
 */
class MessagesService extends AbstractService
{
    /**
     * 给用户发消息.
     *
     * @var string
     */
    const TARGET_USERS = 'users';

    /**
     * 给群发消息.
     *
     * @var string
     */
    const TARGET_CHATGROUPS = 'chatgroups';

    /**
     * 给聊天室发消息.
     *
     * @var string
     */
    const TARGET_CHATROOMS = 'chatrooms';

    /**
     * 发送文本消息.
     *
     *
     * @param string       $from       发送方
     * @param string|array $to         接收方
     * @param string       $text       文本消息
     * @param array        $ext        扩展消息
     * @param string       $targetType 接收方类型
     */
    public function sendText(string $from, $to, string $text, array $ext = [], string $targetType = self::TARGET_USERS)
    {
        $msg = [
            'type' => 'txt',
            'msg'  => $text,
        ];

        return $this->send($from, $to, $msg, $ext, $targetType);
    }

    /**
     * 发送图片信息,.
     *
     * @param string       $from       发送方
     * @param string|array $to         接收方
     * @param string       $filePath   图片路径
     * @param string       $fileName   图片名
     * @param array        $ext        扩展信息
     * @param string       $targetType 接收方类型
     */
    public function sendPicture(string $from, $to, string $filePath, string $fileName = 'picture', array $ext = [], string $targetType = self::TARGET_USERS)
    {
        $headers = [
            'restrict-access' => true,
        ];
        $multipart = [
            [
                'name'     => 'file',
                'contents' => file_get_contents($filePath),
            ],
        ];
        list($width, $height) = getimagesize($filePath);
        $file = $this->getResult(self::METHOD_POST, '/chatfiles', [], $headers, $multipart);
        $msg = [
            'type'     => 'img',
            'uri'      => $file['uri'].'/'.$file['entities'][0]['uuid'],
            'filename' => $fileName,
            'secret'   => $file['entities'][0]['share-secret'],
            'size'     => [
                'width'  => $width,
                'height' => $height,
            ],
        ];

        return $this->send($from, $to, $msg, $ext, $targetType);
    }

    public function send(string $from, $to, $msg, array $ext = [], string $targetType = self::TARGET_USERS)
    {
        $body = [
            'target_type' => $targetType,
            'target'      => (array) $to,
            'msg'         => $msg,
            'from'        => $from,
        ];
        if (!empty($ext)) {
            $body['ext'] = $ext;
        }

        return $this->getResult(self::METHOD_POST, '/messages', $body);
    }
}
