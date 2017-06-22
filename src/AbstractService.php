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

use Eelly\OAuth2\Client\Provider\EasemobProvider;
use GuzzleHttp\Exception\BadResponseException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use UnexpectedValueException;

/**
 * @author hehui<hehui@eelly.net>
 */
abstract class AbstractService
{
    /**
     * @var string HTTP method
     */
    protected const METHOD_GET = 'GET';

    /**
     * @var string HTTP method
     */
    protected const METHOD_POST = 'POST';

    /**
     * @var string HTTP method
     */
    protected const METHOD_PUT = 'PUT';

    /**
     * @var string HTTP method
     */
    protected const METHOD_DELETE = 'DELETE';

    /**
     * @var EasemobProvider
     */
    protected $provider;

    protected $token;

    public function __construct(EasemobProvider $provider)
    {
        $this->provider = $provider;
        $this->token = $this->provider->getAccessToken('client_credentials')->getToken();
    }

    public function getParsedResponse(RequestInterface $request)
    {
        try {
            $response = $this->provider->getResponse($request);
        } catch (BadResponseException $e) {
            $response = $e->getResponse();
        }

        $parsed = $this->parseResponse($response);
        $this->checkResponse($response, $parsed);

        return $parsed;
    }

    protected function parseResponse(ResponseInterface $response)
    {
        $content = (string) $response->getBody();

        try {
            return $this->parseJson($content);
        } catch (UnexpectedValueException $e) {
            if ($response->getStatusCode() == 500) {
                throw new UnexpectedValueException(
                    'An Easemob server error was encountered that did not contain a JSON body',
                    0,
                    $e
                    );
            }

            return $content;
        }
    }

    protected function parseJson($content)
    {
        $content = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new UnexpectedValueException(sprintf(
                'Failed to parse JSON response: %s',
                json_last_error_msg()
                ));
        }

        return $content;
    }

    abstract protected function checkResponse(ResponseInterface $response, $data): void;
}
