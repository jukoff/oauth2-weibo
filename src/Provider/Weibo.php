<?php

namespace Jukoff\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Weibo extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * API url
     *
     * @var string
     */
    const BASE_API_URL = 'https://api.weibo.com/';

    /**
     * Current API version
     *
     * @var int
     */
    protected $currentApiVersion = 2;

    /**
     * Sets current API version
     *
     * @param int $version
     */
    public function setApiVersion($version)
    {
        $this->currentApiVersion = $version;
    }

    /**
     * Returns current API version
     *
     * @return int
     */
    public function getApiVersion()
    {
        return $this->currentApiVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAuthorizationUrl()
    {
        return self::BASE_API_URL . 'oauth2/authorize';
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return self::BASE_API_URL . 'oauth2/access_token';
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token, $uid = null)
    {
        $parameters = [
            'access_token' => $token->getToken(),
            'uid' => (int) $uid,
        ];

        return self::BASE_API_URL . $this->currentApiVersion . '/users/show.json?' . http_build_query($parameters);
    }

    /**
     * Returns resource owner ID
     *
     * @param AccessToken $token
     *
     * @return string
     */
    protected function getResourceOwnerId(AccessToken $token)
    {
        $parameters = [
            'access_token' => $token->getToken(),
        ];

        return self::BASE_API_URL . $this->currentApiVersion . '/account/get_uid.json?' . http_build_query($parameters);
    }

    /**
     * Requests resource owner details.
     *
     * @param  AccessToken $token
     * @return mixed
     */
    protected function fetchResourceOwnerDetails(AccessToken $token)
    {
        $uid = $this->getUid($token);

        $url = $this->getResourceOwnerDetailsUrl($token, $uid['uid']);

        $request = $this->getAuthenticatedRequest(self::METHOD_GET, $url, $token);

        return $this->getResponse($request);
    }

    /**
     * Returns user id data from API
     *
     * @param AccessToken $token
     *
     * @return mixed
     */
    protected function getUid(AccessToken $token)
    {
        $url = $this->getResourceOwnerId($token);

        $request = $this->getRequest(self::METHOD_GET, $url);

        return $this->getResponse($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultScopes()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException(
                $data['error'] ?: $response->getReasonPhrase(),
                $response->getStatusCode(),
                $response
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new WeiboResourceOwner($response);
    }
}