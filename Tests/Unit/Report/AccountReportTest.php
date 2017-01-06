<?php

/*
 * This file is part of SemaphoreSmsBundle.
 *
 * Yan Barreta <augustianne.barreta@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yan\Bundle\SemaphoreSmsBundle\Tests\Unit\Report;

use Yan\Bundle\SemaphoreSmsBundle\Report\AccountReport;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpFoundation\Request;

/**
 * Unit test for AccountReport
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class AccountReportTest extends \PHPUnit_Framework_TestCase
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

    public function getComposeParametersData()
    {
        return array(
            array(
                'ThisIsATestApiKey',
                array(
                    'apikey' => 'ThisIsATestApiKey'
                )
            )
        );
    }

    public function getAccountBalanceData()
    {
        return array(
            array('{"account_id":"00001","account_name":"Account Name","status":"Active","credit_balance":"1000"}', 1000),
            array('{"account_id":"00001","account_name":"Account Name","status":"Active","credit_balance":"2345"}', 2345)
        );
    }

    public function getAccountBalanceThrowsExceptionData()
    {
        return array(
            array('This is Not an array')
        );
    }

    public function getAccountStatusData()
    {
        return array(
            array('{"account_id":"00001","account_name":"Account Name","status":"Active","credit_balance":"1000"}', "Active"),
            array('{"account_id":"00001","account_name":"Account Name","status":"Active","credit_balance":"2345"}', "Active")
        );
    }

    public function getAccountStatusThrowsExceptionData()
    {
        return array(
            array('This is Not an array')
        );
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Report/AccountReport::composeParameters
     * @dataProvider getComposeParametersData
     */
    public function testGetComposeParameters($apiKeyValue, $expectedValue)
    {
        $curlMock = $this->getCurlMock();

        $configurationMock = $this->getConfigurationMock();
        $configurationMock->expects($this->any())
            ->method('getApiKey')
            ->will($this->returnValue($apiKeyValue));

        $sut = new AccountReport($configurationMock, $curlMock);

        $this->assertEquals($expectedValue, $sut->composeParameters());
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Report/AccountReport::getAccountBalance
     * @dataProvider getAccountBalanceData
     */
    public function testGetAccountBalance($dummyReturn, $expectedValue)
    {
        $curlMock = $this->getCurlMock();
        $curlMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($dummyReturn));
        
        $configurationMock = $this->getConfigurationMock();

        $sut = new AccountReport($configurationMock, $curlMock);

        $this->assertEquals($expectedValue, $sut->getAccountBalance());
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Report/AccountReport::getAccountBalance
     * @dataProvider getAccountBalanceThrowsExceptionData
     */
    public function testGetAccountBalanceThrowsException($dummyReturn)
    {
        $curlMock = $this->getCurlMock();
        $curlMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($dummyReturn));

        $configurationMock = $this->getConfigurationMock();

        $sut = new AccountReport($configurationMock, $curlMock);
        $this->setExpectedException('\Yan\Bundle\SemaphoreSmsBundle\Exception\DeliveryFailureException');
        $sut->getAccountBalance();
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Report/AccountReport::getAccountStatus
     * @dataProvider getAccountStatusData
     */
    public function testGetAccountStatus($dummyReturn, $expectedValue)
    {
        $curlMock = $this->getCurlMock();
        $curlMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($dummyReturn));

        $configurationMock = $this->getConfigurationMock();

        $sut = new AccountReport($configurationMock, $curlMock);

        $this->assertEquals($expectedValue, $sut->getAccountStatus());
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Report/AccountReport::getAccountStatus
     * @dataProvider getAccountStatusThrowsExceptionData
     */
    public function testGetAccountStatusThrowsException($dummyReturn)
    {
        $curlMock = $this->getCurlMock();
        $curlMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($dummyReturn));

        $configurationMock = $this->getConfigurationMock();

        $sut = new AccountReport($configurationMock, $curlMock);
        $this->setExpectedException('\Yan\Bundle\SemaphoreSmsBundle\Exception\DeliveryFailureException');
        $sut->getAccountStatus();
    }
}
