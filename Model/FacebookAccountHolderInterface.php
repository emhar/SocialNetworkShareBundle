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

interface FacebookAccountHolderInterface
{
    /**
     * @return string|null
     */
    public function getFacebookId();

    /**
     * @param string|null $facebookId
     */
    public function setFacebookId(string $facebookId = null);

    /**
     * @return string|null
     */
    public function getFacebookAccessToken();

    /**
     * @param string|null $facebookAccessToken
     */
    public function setFacebookAccessToken(string $facebookAccessToken = null);
}