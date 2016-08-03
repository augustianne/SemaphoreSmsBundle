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

use Yan\Bundle\SemaphoreSmsBundle\Sms\BulkSmsSender;
use Yan\Bundle\SemaphoreSmsBundle\Sms\Message;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpFoundation\Request;

/**
 * Unit test for BulkSmsSender
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class BulkSmsSenderTest extends \PHPUnit_Framework_TestCase
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

    public function getComposeParametersData()
    {
        return array(
            array(
                null, '09173149060,09173149060', 'Message', 'Sender', 'ThisIsATestApiKey', 
                array(
                    'api' => 'ThisIsATestApiKey',
                    'number' => '09173149060,09173149060',
                    'message' => 'Message',
                    'from' => 'Sender'
                )
            )
        );
    }

    public function getSmsDeliveryData()
    {
        return array(
            array(
                null, array('09173149060', '09173149060'), 'Message', 'Sender', 'ThisIsATestApiKey', '09177028537',
                array(
                    'api' => 'ThisIsATestApiKey',
                    'number' => '09177028537',
                    'message' => 'Sent to: 09173149060,09173149060. Message',
                    'from' => 'Sender'
                )
            ),
            array(
                null, array('09173149060', '09173149060'), 'Message', 'Sender', 'ThisIsATestApiKey', null,
                array(
                    'api' => 'ThisIsATestApiKey',
                    'number' => '09173149060,09173149060',
                    'message' => 'Message',
                    'from' => 'Sender'
                )
            )
        );
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/BulkSmsSender::composeParameters
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
            ->will($this->returnValue(array('09173149060', '09173149061')));

        $sut = new BulkSmsSender($configurationMock, $curlMock);

        $this->assertEquals($expectedValue, $sut->composeParameters($messageMock));

    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/BulkSmsSender::composeParameters
     * @dataProvider getSmsDeliveryData
     */
    public function testSmsDeliveryAddressOnComposeParameters($fromValue, $formatNumberValue, $messageValue, $senderNameValue, $apiKeyValue, $smsDeliveryAddress, $expectedValue)
    {
        $curlMock = $this->getCurlMock();

        $configurationMock = $this->getConfigurationMock();
        $configurationMock->expects($this->any())
            ->method('getApiKey')
            ->will($this->returnValue($apiKeyValue));

        $configurationMock->expects($this->any())
            ->method('getSenderName')
            ->will($this->returnValue($senderNameValue));

        $configurationMock->expects($this->any())
            ->method('getSmsDeliveryAddress')
            ->will($this->returnValue($smsDeliveryAddress));

        $sut = new BulkSmsSender($configurationMock, $curlMock);
        
        $message = new Message();
        $message->setFrom($fromValue);
        $message->setContent($messageValue);
        $message->setNumbers($formatNumberValue);

        $this->assertEquals($expectedValue, $sut->composeParameters($message));

    }
    
}
