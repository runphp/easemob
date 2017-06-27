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

    protected function getParsedResponse(RequestInterface $request)
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

    /**
     * @param string $method
     * @param string $uri
     * @param array  $body
     *
     * @return array
     */
    protected function getResult(string $method, string $uri, array $body = [])
    {
        $options = [];
        if (!empty($body)) {
            $options['body'] = json_encode($body);
        }
        $uri = $this->provider->getBaseAuthorizationUrl().$uri;
        $request = $this->provider->getAuthenticatedRequest($method, $uri, $this->token, $options);
        $parsed = $this->getParsedResponse($request);

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
