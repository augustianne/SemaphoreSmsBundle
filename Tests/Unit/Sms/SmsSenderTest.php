<?php

/*
 * This file is part of SemaphoreSmsBundle.
 *
 * Yan Barreta <augustianne.barreta@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yan\Bundle\SemaphoreSmsBundle\Tests\Unit\Sms;

use Yan\Bundle\SemaphoreSmsBundle\Sms\SmsSender;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpFoundation\Request;

/**
 * Unit test for SingleSmsSender
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class SmsSenderTest extends \PHPUnit_Framework_TestCase
{
    private $sut;
    private $root;
    
    public function getConfigurationMock()
    {
        $configurationMock = $this->getMockBuilder('Yan\Bundle\SemaphoreSmsBundle\Sms\SemaphoreSmsConfiguration')
            ->disableOriginalConstructor()
            ->getMock();

        return $configurationMock;
    }

    public function getCurlMock()
    {
        $curlMock = $this->getMockBuilder('Yan\Bundle\SemaphoreSmsBundle\Request\Curl')
            ->disableOriginalConstructor()
            ->getMock();

        return $curlMock;
    }

    public function getMessageMock()
    {
        $messageMock = $this->getMockBuilder('Yan\Bundle\SemaphoreSmsBundle\Sms\Message')
            ->disableOriginalConstructor()
            ->getMock();

        return $messageMock;
    }

    public function getMessageComposerMock()
    {
        $messageComposerMock = $this->getMockBuilder('Yan\Bundle\SemaphoreSmsBundle\Sms\MessageComposer')
            ->disableOriginalConstructor()
            ->getMock();

        return $messageComposerMock;
    }

    public function getGetSenderData()
    {
        return array(
            array(null, 'Sender Name', 'Sender Name'),
            array('', 'Sender Name', 'Sender Name'),
            array('Sender Name', 'Sender Name 2', 'Sender Name'),
            array(null, null, null)
        );
    }

    public function getSendInvalidResultValues()
    {
        return array(
            array(
                false, 
                true, 
                'This is a non-json string'
            )
        );
    }

    public function getSendFailureValidResultValues()
    {
        return array(
            array(
                false, 
                null
            )
        );
    }

    public function getSendSuccessValidResultValues()
    {
        return array(
            array(
                '{"status":"success","message":"Delivered"}', 
                '{"status":"success","message":"Sent to Network"}', 
                '{"status":"success","message":"Message Queued"}'
            )
        );
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/SmsSender::getUrl
     */
    public function testGetUrl()
    {
        $value = 'http://api.semaphore.co/api/sms';
        $stub = $this->getMockBuilder('\Yan\Bundle\SemaphoreSmsBundle\Sms\SmsSender')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $stub->expects($this->any())
             ->method('initUrl')
             ->will($this->returnValue($value));

        $this->assertEquals($value, $stub->getUrl());
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/SmsSender::getSender
     * @dataProvider getGetSenderData
     */
    public function testGetSender($messageValue, $senderNameValue, $expectedValue)
    {
        $curlMock = $this->getCurlMock();

        $configurationMock = $this->getConfigurationMock();
        $configurationMock->expects($this->any())
            ->method('getSenderName')
            ->will($this->returnValue($senderNameValue));

        $messageMock = $this->getMessageMock();
        $messageMock->expects($this->any())
            ->method('getFrom')
            ->will($this->returnValue($messageValue));

        $messageComposerMock = $this->getMessageComposerMock();

        $value = 'http://api.semaphore.co/api/sms';
        $stub = $this->getMockBuilder('\Yan\Bundle\SemaphoreSmsBundle\Sms\SmsSender')
            ->setConstructorArgs(array($configurationMock, $curlMock, $messageComposerMock))
            ->getMockForAbstractClass();

        $stub->expects($this->any())
             ->method('initUrl')
             ->will($this->returnValue($value));

        $this->assertEquals($expectedValue, $stub->getSender($messageMock));

    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/SmsSender::send
     * @dataProvider getSendInvalidResultValues
     */
    public function testSendReturnsInvalidReturnString($value)
    {
        $curlMock = $this->getCurlMock();
        $curlMock->expects($this->any())
            ->method('post')
            ->will($this->returnValue($value));

        $configurationMock = $this->getConfigurationMock();
        $messageMock = $this->getMessageMock();
        
        $messageComposerMock = $this->getMessageComposerMock();
        $messageComposerMock->expects($this->any())
            ->method('compose')
            ->will($this->returnValue(array($messageMock)));

        $stub = $this->getMockBuilder('\Yan\Bundle\SemaphoreSmsBundle\Sms\SmsSender')
            ->setConstructorArgs(array($configurationMock, $curlMock, $messageComposerMock))
            ->getMockForAbstractClass();

        $value = 'http://api.semaphore.co/api/sms';
        $stub->expects($this->any())
             ->method('initUrl')
             ->will($this->returnValue($value));

         $stub->expects($this->any())
             ->method('composeParameters')
             ->will($this->returnValue(array()));

        $this->setExpectedException('\Yan\Bundle\SemaphoreSmsBundle\Exception\DeliveryFailureException');
        $stub->send($messageMock);
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/SmsSender::send
     * @dataProvider getSendFailureValidResultValues
     */
    public function testSendFailureReturnsValidJsonString($value)
    {
        $curlMock = $this->getCurlMock();
        $curlMock->expects($this->any())
            ->method('post')
            ->will($this->returnValue($value));

        $configurationMock = $this->getConfigurationMock();
        $messageMock = $this->getMessageMock();
        
        $messageComposerMock = $this->getMessageComposerMock();
        $messageComposerMock->expects($this->any())
            ->method('compose')
            ->will($this->returnValue(array($messageMock)));

        $stub = $this->getMockBuilder('\Yan\Bundle\SemaphoreSmsBundle\Sms\SmsSender')
            ->setConstructorArgs(array($configurationMock, $curlMock, $messageComposerMock))
            ->getMockForAbstractClass();

        $value = 'http://api.semaphore.co/api/sms';
        $stub->expects($this->any())
             ->method('initUrl')
             ->will($this->returnValue($value));

         $stub->expects($this->any())
             ->method('composeParameters')
             ->will($this->returnValue(array()));

        $this->setExpectedException('\Yan\Bundle\SemaphoreSmsBundle\Exception\DeliveryFailureException');
        $stub->send($messageMock);
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/SmsSender::send
     * @dataProvider getSendSuccessValidResultValues
     */
    public function testSendSuccessReturnsValidJsonString($value)
    {
        $curlMock = $this->getCurlMock();
        $curlMock->expects($this->any())
            ->method('post')
            ->will($this->returnValue($value));

        $configurationMock = $this->getConfigurationMock();
        $messageMock = $this->getMessageMock();
        
        $messageComposerMock = $this->getMessageComposerMock();
        $messageComposerMock->expects($this->any())
            ->method('compose')
            ->will($this->returnValue(array($messageMock)));

        $stub = $this->getMockBuilder('\Yan\Bundle\SemaphoreSmsBundle\Sms\SmsSender')
            ->setConstructorArgs(array($configurationMock, $curlMock, $messageComposerMock))
            ->getMockForAbstractClass();

        $value = 'http://api.semaphore.co/api/sms';
        $stub->expects($this->any())
             ->method('initUrl')
             ->will($this->returnValue($value));

         $stub->expects($this->any())
             ->method('composeParameters')
             ->will($this->returnValue(array()));

        $this->assertTrue($stub->send($messageMock));
    }
    
}
