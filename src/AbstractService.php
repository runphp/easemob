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

use Eelly\Easemob\Exception\ClientException as EasemobClientException;
use Eelly\Easemob\Exception\ServerException as EasemobServerException;
use Eelly\OAuth2\Client\Provider\EasemobProvider;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\MultipartStream;
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
        $exceptionClass = null;
        try {
            $response = $this->provider->getResponse($request);
        } catch (ServerException $e) {
            $response = $e->getResponse();
            $exceptionClass = EasemobServerException::class;
        } catch (ClientException $e) {
            $response = $e->getResponse();
            $exceptionClass = EasemobClientException::class;
        }
        $data = $this->parseResponse($response);

        if (null !== $exceptionClass && !empty($data['error'])) {
            throw new $exceptionClass($data['error'], $response->getStatusCode(), $data);
        }

        return $data;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $body
     * @param array  $headers
     * @param array  $multipart
     *
     * @return array
     */
    protected function getResult(string $method, string $uri, array $body = [], array $headers = [], array $multipart = [])
    {
        $options = [];
        if (!empty($body)) {
            $options['body'] = json_encode($body);
        }
        if (!empty($headers)) {
            $options['headers'] = $headers;
        }
        if (!empty($multipart)) {
            $options['body'] = new MultipartStream($multipart);
        }
        $uri = $this->provider->getBaseAuthorizationUrl().$uri;
        $options['debug'] = true;
        $request = $this->provider->getAuthenticatedRequest($method, $uri, $this->token, $options);
        $parsed = $this->getParsedResponse($request);

        return $parsed;
    }

    protected function parseResponse(ResponseInterface $response)
    {
        $content = (string) $response->getBody();

        try {
            return $this->parseJson($content);
            // @codeCoverageIgnoreStart
        } catch (UnexpectedValueException $e) {
            if ($response->getStatusCode() == 500) {
                throw new UnexpectedValueException(
                    'An Easemob server error was encountered that did not contain a JSON body',
                    0,
                    $e
                    );
            }

            return $content;
            // @codeCoverageIgnoreEnd
        }
    }

    protected function parseJson($content)
    {
        $content = json_decode($content, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // @codeCoverageIgnoreStart
            throw new UnexpectedValueException(sprintf(
                'Failed to parse JSON response: %s',
                json_last_error_msg()
                ));
            // @codeCoverageIgnoreEnd
        }

        return $content;
    }
}
