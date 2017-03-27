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
    protected $baseUrl = 'https://api.weibo.com/';

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
        return $this->baseUrl . 'oauth2/authorize';
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseAccessTokenUrl(array $params)
    {
        return $this->baseUrl . 'oauth2/access_token';
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        $parameters = [
            'access_token' => $token->getToken(),
        ];

        return $this->baseUrl . $this->currentApiVersion . '/users/show.json?' . http_build_query($parameters);
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