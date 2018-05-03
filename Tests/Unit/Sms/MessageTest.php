<?php

namespace Yan\Bundle\SemaphoreSmsBundle\Tests\Unit\DependencyInjection;

use Yan\Bundle\SemaphoreSmsBundle\Sms\Message;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpFoundation\Request;

class MessageTest extends \PHPUnit_Framework_TestCase
{
    private $sut;
    private $root;
    
    protected function setUp()
    {
        $this->sut = new Message();
    }

    public function getNumberValues()
    {
        return array(
            array(
                array('09173149060'),
                '09173149060'
            ),
            array(
                array('09173149060', '09275293991'),
                '09173149060,09275293991',
            ),
            array(
                array('09173149060', '09275293991', '09173149060', '09173149060'),
                '09173149060,09275293991',
            ),
            array(
                array('09173149060', '09173149060', '09173149060', '09173149060'),
                '09173149060',
            )
        );
    }

    public function getFromValues()
    {
        return array(
            array('Sender'),
            array('Sender Name'),
            array(''),
            array(null)
        );
    }    

    public function getContentValues()
    {
        return array(
            array('Content Lorem ipsum etcetera etcetera'),
            array('Test'),
            array(''),
            array(null)
        );
    }    

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/Message::setNumber
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/Message::getNumbers
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/Message::formatNumber
     * @dataProvider getNumberValues
     */
    public function testAddingGettingOfNumbers($values, $result)
    {
        foreach ($values as $value) {
            $this->sut->addNumber($value);
        }
        
        $this->assertEquals($result, $this->sut->formatNumber());

        $diff = array_diff($values, $this->sut->getNumbers());
        $this->assertTrue(empty($diff));
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/Message::setNumber
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/Message::getNumbers
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/Message::formatNumber
     * @dataProvider getNumberValues
     */
    public function testSettingGettingOfNumbers($values, $result)
    {
        $this->sut->setNumbers($values);
        
        $this->assertEquals($result, $this->sut->formatNumber());

        $diff = array_diff($values, $this->sut->getNumbers());
        $this->assertTrue(empty($diff));
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/Message::setFrom
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/Message::getFrom
     * @dataProvider getFromValues
     */
    public function testSettingGettingOfFrom($value)
    {
        
        $this->sut->setFrom($value);
        
        $this->assertEquals($value, $this->sut->getFrom());
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/Message::setContent
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/Message::getContent
     * @dataProvider getContentValues
     */
    public function testSettingGettingOfContent($value)
    {
        
        $this->sut->setContent($value);
        
        $this->assertEquals($value, $this->sut->getContent());
    }
}
