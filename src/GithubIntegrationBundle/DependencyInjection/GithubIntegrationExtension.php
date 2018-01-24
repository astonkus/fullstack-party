<?php

namespace GithubIntegrationBundle\DependencyInjection;

use GuzzleHttp\Client;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class GithubIntegrationExtension
 */
class GithubIntegrationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $client = $container->getDefinition('github_integration.client');
        $client->addArgument($config['settings']);
        $client->setLazy(true);

        $apiClient = new Definition(
            Client::class,
            [
                ['base_uri' => $config['settings']['base_api_url']]
            ]
        );

        $apiClient->setLazy(true);

        $client->addArgument($apiClient);

    }
}
