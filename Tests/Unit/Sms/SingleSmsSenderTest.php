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

use Yan\Bundle\SemaphoreSmsBundle\Sms\SingleSmsSender;

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
class SingleSmsSenderTest extends \PHPUnit_Framework_TestCase
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

    public function getComposeParametersData()
    {
        return array(
            array(
                null, '09173149060', 'Message', 'Sender', 'ThisIsATestApiKey',
                array(
                    'apikey' => 'ThisIsATestApiKey',
                    'number' => '09173149060',
                    'message' => 'Message',
                    'sendername' => 'Sender'
                )
            )
        );
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/SingleSmsSender::composeParameters
     */
    public function testGetComposeParametersThrowsException()
    {
        $curlMock = $this->getCurlMock();
        $configurationMock = $this->getConfigurationMock();
        
        $messageMock = $this->getMessageMock();
        $messageMock->expects($this->any())
            ->method('getNumbers')
            ->will($this->returnValue(array('09173149060', '09173149061')));

        $messageComposerMock = $this->getMessageComposerMock();

        $sut = new SingleSmsSender($configurationMock, $curlMock, $messageComposerMock);
        $this->setExpectedException('\InvalidArgumentException');
        $sut->composeParameters($messageMock);
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/SingleSmsSender::composeParameters
     * @dataProvider getComposeParametersData
     */
    public function testGetComposeParameters($fromValue, $formatNumberValue, $messageValue, $senderNameValue, $apiKeyValue, $expectedValue)
    {
        $curlMock = $this->getCurlMock();

        $configurationMock = $this->getConfigurationMock();
        $configurationMock->expects($this->any())
            ->method('getApiKey')
            ->will($this->returnValue($apiKeyValue));

        $configurationMock->expects($this->any())
            ->method('getSenderName')
            ->will($this->returnValue($senderNameValue));

        $messageMock = $this->getMessageMock();
        $messageMock->expects($this->any())
            ->method('getFrom')
            ->will($this->returnValue($fromValue));

        $messageMock->expects($this->any())
            ->method('formatNumber')
            ->will($this->returnValue($formatNumberValue));

        $messageMock->expects($this->any())            
            ->method('getContent')
            ->will($this->returnValue($messageValue));

        $messageMock->expects($this->any())            
            ->method('getNumbers')
            ->will($this->returnValue(array('09173149060')));

        $messageComposerMock = $this->getMessageComposerMock();

        $sut = new SingleSmsSender($configurationMock, $curlMock, $messageComposerMock);

        $this->assertEquals($expectedValue, $sut->composeParameters($messageMock));

    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/SingleSmsSender::initUrl
     */
    public function testInitUrl()
    {
        $curlMock = $this->getCurlMock();
        $configurationMock = $this->getConfigurationMock();
        $messageComposerMock = $this->getMessageComposerMock();

        $sut = new SingleSmsSender($configurationMock, $curlMock, $messageComposerMock);

        $this->assertEquals('http://beta.semaphore.co/api/v4/messages', $sut->initUrl());

    }
    
}
