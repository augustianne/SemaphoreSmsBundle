<?php

namespace Yan\Bundle\SemaphoreSmsBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Bundle Extension
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class SemaphoreSmsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('semaphore_sms', $config);
        
        $container->setParameter('semaphore_sms.api_key', $config['api_key']);
        $container->setParameter('semaphore_sms.failure_delivery_address', $config['failure_delivery_address']);
        $container->setParameter('semaphore_sms.sms_delivery_address', $config['sms_delivery_address']);
        $container->setParameter('semaphore_sms.limit_messages', $config['limit_messages']);
        $container->setParameter('semaphore_sms.disable_delivery', $config['disable_delivery']);
        
        if (isset($config['sender_name'])) {
            $container->setParameter('semaphore_sms.sender_name', $config['sender_name']);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
