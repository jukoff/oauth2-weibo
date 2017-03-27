<?php

namespace Jukoff\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class WeiboResourceOwner implements ResourceOwnerInterface
{
    /**
     * Raw response
     *
     * @var array
     */
    protected $response;
    /**
     * Creates new resource owner.
     *
     * @param array $response
     */
    public function __construct(array $response = [])
    {
        $this->response = $response;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->response['id'];
    }

    /**
     * Returns screen name
     *
     * @return string|null
     */
    public function getDisplayName()
    {
        return $this->response['screen_name'] ?: null;
    }

    /**
     * Returns name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->response['name'] ?: null;
    }

    /**
     * Returns resource url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->response['url'];
    }

    /**
     * Returns avatar url
     *
     * @return string|null
     */
    public function getAvatar()
    {
        return $this->response['profile_image_url'] ?: null;
    }

    /**
     * Returns large avatar url
     *
     * @return string|null
     */
    public function getLargeAvatar()
    {
        return $this->response['avatar_large'] ?: null;
    }

    /**
     * @return int|null
     */
    public function getFollowers()
    {
        return $this->response['followers_count'] ?: null;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->response;
    }
}