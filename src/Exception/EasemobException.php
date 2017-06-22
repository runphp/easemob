<?php

declare(strict_types=1);
/*
 * PHP version 7.1
 *
 * @copyright Copyright (c) 2012-2017 EELLY Inc. (https://www.eelly.com)
 * @link      https://api.eelly.com
 * @license   衣联网版权所有
 */

namespace Eelly\Easemob\Exception;

class EasemobException extends \Exception
{
    /**
     * @var mixed
     */
    protected $response;

    /**
     * @param string       $message
     * @param int          $code
     * @param array|string $response The response body
     */
    public function __construct($message, $code, $response)
    {
        $this->response = $response;

        parent::__construct($message, $code);
    }

    /**
     * Returns the exception's response body.
     *
     * @return array|string
     */
    public function getResponseBody()
    {
        return $this->response;
    }
}
