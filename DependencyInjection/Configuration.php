<?php

/*
 * This file is part of the EmharSocialNetworkShareBundle bundle.
 *
 * (c) Emmanuel Harleaux
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Emhar\SocialNetworkShareBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * {@inheritDoc}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('emhar_social_network_share')
            ->children()
            ->scalarNode('facebook_app_id')->isRequired()->end()
            ->scalarNode('facebook_app_secret')->isRequired()->end()
            ->scalarNode('twitter_consumer_key')->isRequired()->end()
            ->scalarNode('twitter_consumer_secret')->isRequired()->end()
            ->end();
        return $treeBuilder;
    }
}
