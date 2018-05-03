<?php

namespace Yan\Bundle\SemaphoreSmsBundle\Tests\Integration\Services;

use Yan\Bundle\SemaphoreSmsBundle\DependencyInjection\SemaphoreSmsExtension;
use Yan\Bundle\SemaphoreSmsBundle\Helper\Test\AppKernel;

use Symfony\Bundle\FrameworkBundle\Console\Helper\DescriptorHelper;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\Yaml\Yaml;


class SemaphoreServiceExistenceTest extends \PHPUnit_Framework_TestCase
{
    public function testServicesYml()
    {
        $services = Yaml::parse(__DIR__.'/../../../Resources/config/services.yml');

        $smsFiles = array_diff(scandir(__DIR__.'/../../../Sms/'), array('.', '..'));
        $reportFiles = array_diff(scandir(__DIR__.'/../../../Report/'), array('.', '..'));
        $requestFiles = array_diff(scandir(__DIR__.'/../../../Request/'), array('.', '..'));
        
        $files = array_diff(
            array_merge($smsFiles, $reportFiles, $requestFiles), 
            array('Message.php', 'CurlRequest.php')
        );

        $serviceClasses = array();
        foreach ($services['services'] as $service) {
            $pathParts = explode('\\', $service['class']);
            $className = end($pathParts);
            $serviceClasses[] = $className;
        }

        foreach ($files as $file) {
            $file = str_replace('.php', '', $file);
            $this->assertTrue(in_array($file, $serviceClasses), sprintf('%s.php is not defined in services.yml', $file));
        }
    }

}
