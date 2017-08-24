<?php

/*
 * This file is part of the EmharSocialNetworkShareBundle bundle.
 *
 * (c) Emmanuel Harleaux
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Emhar\SocialNetworkShareBundle\Model;

interface TwitterAccountHolderInterface
{
    /**
     * @return string|null
     */
    public function getTwitterId();

    /**
     * @param string|null $twitterId
     */
    public function setTwitterId(string $twitterId = null);

    /**
     * @return string|null
     */
    public function getTwitterAccessToken();

    /**
     * @param string|null $twitterAccessToken
     */
    public function setTwitterAccessToken(string $twitterAccessToken = null);

    /**
     * @return string|null
     */
    public function getTwitterAccessTokenSecret();

    /**
     * @param string|null $twitterAccessTokenSecret
     */
    public function setTwitterAccessTokenSecret(string $twitterAccessTokenSecret = null);
}