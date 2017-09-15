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

use Abraham\TwitterOAuth\TwitterOAuth;
use Abraham\TwitterOAuth\TwitterOAuthException;
use Emhar\SocialNetworkShareBundle\Exception\InvalidAccessTokenException;
use Emhar\SocialNetworkShareBundle\Model\TwitterAccountHolderInterface;

class TwitterProvider
{
    /**
     * @var TwitterOAuth
     */
    protected $twitter;

    /**
     * @param string $twitterConsumerKey
     * @param string $twitterConsumerSecret
     */
    public function __construct(string $twitterConsumerKey, string $twitterConsumerSecret)
    {
        $this->twitter = new TwitterOAuth($twitterConsumerKey, $twitterConsumerSecret);
    }


    public function getAuthorizeUrl(string $redirectUrl)
    {
        $this->twitter->setOauthToken(null, null);
        $request_token = $this->twitter->oauth('oauth/request_token', array('oauth_callback' => $redirectUrl));
        return $this->twitter->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
    }

    /**
     * @param TwitterAccountHolderInterface $holder
     * @param string $oauthVerifier
     * @param string $oauthToken
     * @throws InvalidAccessTokenException
     */
    public function setAccessTokenAndId(TwitterAccountHolderInterface $holder, string $oauthVerifier, string $oauthToken)
    {
        try {
            $this->twitter->setOauthToken($oauthToken, $oauthVerifier);
            $accessToken = $this->twitter->oauth('oauth/access_token', ['oauth_verifier' => $oauthVerifier]);
            $holder->setTwitterId($accessToken['user_id']);
            $holder->setTwitterAccessToken($accessToken['oauth_token']);
            $holder->setTwitterAccessTokenSecret($accessToken['oauth_token_secret']);
        } catch (TwitterOAuthException $e) {
            throw new InvalidAccessTokenException('Access verifier invalid');
        }
    }

    /**
     * @param TwitterAccountHolderInterface $holder
     * @return bool
     */
    public function isAccessTokenValid(TwitterAccountHolderInterface $holder): bool
    {
        $this->twitter->setOauthToken($holder->getTwitterAccessToken(), $holder->getTwitterAccessTokenSecret());
        $this->twitter->get('account/verify_credentials');
        return $this->twitter->getLastHttpCode() === 200;
    }

    /**
     * @param TwitterAccountHolderInterface $holder
     * @param string $message
     * @throws InvalidAccessTokenException
     * @throws \Abraham\TwitterOAuth\TwitterOAuthException
     */
    public function post(TwitterAccountHolderInterface $holder, string $message)
    {
        if ($holder->getTwitterId()) {
            if (!$this->isAccessTokenValid($holder)) {
                throw new InvalidAccessTokenException('Access token provided is invalid');
            }
            $this->twitter->setTimeouts(15, 15);
            $this->twitter->setOauthToken($holder->getTwitterAccessToken(), $holder->getTwitterAccessTokenSecret());
            $response = $this->twitter->post('statuses/update', array(
                'status' => $message
            ));
            if ($this->twitter->getLastHttpCode() !== 200) {
                $message = 'A problem occurred in twitter post';
                if ($error = array_pop($response->errors)) {
                    $message .= ': ' . $error->message;
                }
                throw new TwitterOAuthException($message);
            }
        }
    }

    /**
     * @param TwitterAccountHolderInterface $holder
     * @param string $url
     * @throws InvalidAccessTokenException
     * @throws \Abraham\TwitterOAuth\TwitterOAuthException
     */
    public function postURL(TwitterAccountHolderInterface $holder, string $url)
    {
        $this->post($holder, $url);
    }
}
