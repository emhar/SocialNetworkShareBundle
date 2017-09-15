<?php

/*
 * This file is part of the EmharSocialNetworkShareBundle bundle.
 *
 * (c) Emmanuel Harleaux
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Emhar\SocialNetworkShareBundle\Provider;

use Emhar\SocialNetworkShareBundle\Exception\InvalidAccessTokenException;
use Emhar\SocialNetworkShareBundle\Model\FacebookAccountHolderInterface;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;

class FacebookProvider
{
    protected $facebook;

    public function __construct(string $facebookId, string $facebookSecret)
    {
        $this->facebook = new Facebook([
            'app_id' => $facebookId,
            'app_secret' => $facebookSecret,
            'default_graph_version' => 'v2.10'
        ]);
    }

    /**
     * @param FacebookAccountHolderInterface $holder
     * @return bool
     */
    public function isAccessTokenValid(FacebookAccountHolderInterface $holder): bool
    {
        return $this->isAccessTokenValueValid($holder->getFacebookAccessToken());
        try {
            $oAuth2Client = $this->facebook->getOAuth2Client();
            $tokenMetadata = $oAuth2Client->debugToken($holder->getFacebookAccessToken());
            $tokenMetadata->validateExpiration();
        } catch (FacebookSDKException $e) {
            return false;
        }
        return true;
    }

    /**
     * @param FacebookAccountHolderInterface $holder
     * @return bool
     */
    public function isAccessTokenValueValid(string $value): bool
    {
        try {
            $oAuth2Client = $this->facebook->getOAuth2Client();
            $tokenMetadata = $oAuth2Client->debugToken($value);
            $tokenMetadata->validateExpiration();
        } catch (FacebookSDKException $e) {
            return false;
        }
        return true;
    }

    /**
     * @param FacebookAccountHolderInterface $holder
     * @return \DateTime|null|false
     */
    public function getAccessTokenExpireDate(FacebookAccountHolderInterface $holder)
    {
        $oAuth2Client = $this->facebook->getOAuth2Client();
        $tokenMetadata = $oAuth2Client->debugToken($holder->getFacebookAccessToken());
        return $tokenMetadata->getExpiresAt();
    }

    /**
     * @param FacebookAccountHolderInterface $holder
     * @throws InvalidAccessTokenException
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function setAccessTokenAndId(FacebookAccountHolderInterface $holder)
    {
        $helper = $this->facebook->getJavaScriptHelper();
        $accessToken = $helper->getAccessToken();
        if (!$this->isAccessTokenValueValid($accessToken->getValue())) {
            throw new InvalidAccessTokenException('Access token provided is invalid');
        }
        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $oAuth2Client = $this->facebook->getOAuth2Client();
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
            }
        }
        $response = $this->facebook->get('/me', $accessToken);
        $me = $response->getGraphUser();
        $holder->setFacebookId($me->getId());
        $holder->setFacebookAccessToken($accessToken->getValue());
    }

    /**
     * @param FacebookAccountHolderInterface $holder
     * @param string $message
     * @throws InvalidAccessTokenException
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function post(FacebookAccountHolderInterface $holder, string $message)
    {
        if ($holder->getFacebookId()) {
            if (!$this->isAccessTokenValid($holder)) {
                throw new InvalidAccessTokenException('Access token provided is invalid');
            }
            $this->facebook->post('/me/feed', array(
                'message' => $message
            ), $holder->getFacebookAccessToken());
        }
    }

    /**
     * @param FacebookAccountHolderInterface $holder
     * @param string $url
     * @throws InvalidAccessTokenException
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function postURL(FacebookAccountHolderInterface $holder, string $url)
    {
        if ($holder->getFacebookId()) {
            if (!$this->isAccessTokenValid($holder)) {
                throw new InvalidAccessTokenException('Access token provided is invalid');
            }
            $this->facebook->post('/me/feed', array(
                'link' => $url
            ), $holder->getFacebookAccessToken());
        }
    }
}
