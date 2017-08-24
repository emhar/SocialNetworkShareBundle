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

use CNOSF\PassSport\AdminBundle\SonataMediaProvider\ImageProvider;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * {@inheritDoc}
 */
class EmharSocialNetworkShareExtension extends Extension
{
    /**
     * {@inheritDoc}
     * @throws \Exception
     * @throws \Symfony\Component\DependencyInjection\Exception\OutOfBoundsException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('emhar.social_network_share.facebook_app_id', $config['facebook_app_id']);
        $container->setParameter('emhar.social_network_share.facebook_app_secret', $config['facebook_app_secret']);
        $container->setParameter('emhar.social_network_share.twitter_consumer_key', $config['twitter_consumer_key']);
        $container->setParameter('emhar.social_network_share.twitter_consumer_secret', $config['twitter_consumer_secret']);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}