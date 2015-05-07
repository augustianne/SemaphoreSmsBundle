<?php

namespace Yan\Bundle\SemaphoreSmsBundle\Tests\Unit\DependencyInjection;

use Yan\Bundle\SemaphoreSmsBundle\DependencyInjection\SemaphoreSmsExtension;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpFoundation\Request;

class SemaphoreSmsExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $sut;
    private $container;
    private $root;
    
    protected function setUp()
    {
        $this->sut = new SemaphoreSmsExtension();
        $this->container = new ContainerBuilder();
        $this->root = 'semaphore_sms';
    }

    public function getConfigValuesThatThrowsException()
    {
        return array(
            array(
                array(), 
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException'
            ),
            array(
                array('not_semaphore_sms'), 
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException'
            ),
            array(
                array($this->root), 
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException'
            ),
            array(
                array($this->root => array()), 
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException'
            ),
            array(array($this->root => array('api_key' => 'test', 'sender_name' => 'test', 'invalid' => 'test')), 
                'Symfony\Component\Config\Definition\Exception\InvalidConfigurationException'
            )
        );
    }    

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/DependencyInjection/SemaphoreSmsExtension::load
     * @dataProvider getConfigValuesThatThrowsException
     */
    public function testThrowExceptionWhenConfigIsInvalid($array, $exception)
    {
        $this->setExpectedException($exception);
        $this->sut->load($array, $this->container);
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/DependencyInjection/SemaphoreSmsExtension::load
     */
    public function testValues()
    {
        $configs = array(
            $this->root => array(
                'api_key' => 'ThisIsATestApiKey',
                'sender_name' => 'Sender Name'
            )
        );

        $this->sut->load($configs, $this->container);

        $this->assertTrue($this->container->hasParameter($this->root.".api_key"));
        $this->assertTrue($this->container->hasParameter($this->root.".sender_name"));
        
        $this->assertEquals($configs[$this->root]['api_key'], $this->container->getParameter($this->root.".api_key"));
        $this->assertEquals($configs[$this->root]['sender_name'], $this->container->getParameter($this->root.".sender_name"));
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/DependencyInjection/SemaphoreSmsExtension::load
     */
    public function testDeliveryAddressValues()
    {
        $configs = array(
            $this->root => array(
                'api_key' => 'ThisIsATestApiKey',
                'failure_delivery_address' => 'augustianne.barreta@gmail.com'
            )
        );

        $this->sut->load($configs, $this->container);

        $this->assertTrue($this->container->hasParameter($this->root.".failure_delivery_address"));
        $this->assertEquals($configs[$this->root]['failure_delivery_address'], $this->container->getParameter($this->root.".failure_delivery_address"));
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/DependencyInjection/SemaphoreSmsExtension::load
     */
    public function testDeliveryAddressDefaultValue()
    {
        $configs = array(
            $this->root => array(
                'api_key' => 'ThisIsATestApiKey'
            )
        );

        $this->sut->load($configs, $this->container);

        $this->assertTrue($this->container->hasParameter($this->root.".failure_delivery_address"));
        $this->assertTrue(is_null($this->container->getParameter($this->root.".failure_delivery_address")));
    }

}
