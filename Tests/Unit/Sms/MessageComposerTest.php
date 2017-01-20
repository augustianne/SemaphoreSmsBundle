<?php

namespace Yan\Bundle\SemaphoreSmsBundle\Tests\Unit\DependencyInjection;

use Yan\Bundle\SemaphoreSmsBundle\Sms\Message;
use Yan\Bundle\SemaphoreSmsBundle\Sms\MessageComposer;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\HttpFoundation\Request;

class MessageComposerTest extends \PHPUnit_Framework_TestCase
{
    public function getConfigurationMock()
    {
        $configurationMock = $this->getMockBuilder('Yan\Bundle\SemaphoreSmsBundle\Sms\SemaphoreSmsConfiguration')
            ->disableOriginalConstructor()
            ->getMock();

        return $configurationMock;
    }

    public function getTestSplitMessageData()
    {
        return array(
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages',
                array(
                    array('Fr', 'AutoDeal:', 'Great', 'news,', 'your', 'listing', 'has', 'been', 'approved', 'and', 'is', 'now', 'live', 'on', 'AutoDeal.com.ph!', 'View', 'your', 'listing', 'at'),
                    array('http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages')
                )
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph!',
                array(
                    array('Fr', 'AutoDeal:', 'Great', 'news,', 'your', 'listing', 'has', 'been', 'approved', 'and', 'is', 'now', 'live', 'on', 'AutoDeal.com.ph!'),
                )
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages',
                array(
                    array('Fr', 'AutoDeal:', 'Great', 'news,', 'your', 'listing', 'has', 'been', 'approved', 'and', 'is', 'now', 'live', 'on', 'AutoDeal.com.ph!', 'View', 'your', 'listing', 'at'),
                    array('http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages')
                )
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages on AutoDeal.com.ph! View your listing at',
                array(
                    array('Fr', 'AutoDeal:', 'Great', 'news,', 'your', 'listing', 'has', 'been', 'approved', 'and', 'is', 'now', 'live'),
                    array('http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages'),
                    array('on', 'AutoDeal.com.ph!', 'View', 'your', 'listing', 'at')
                )
            ),
            array(
                'Fr AutoDeal: Your listing for the 2010 Toyota Innova E Diesel MT has now expired. To renew your listing go http://www.staging4.autodeal.com.ph/account/used-cars/inquiries',
                array(
                    array('Fr', 'AutoDeal:', 'Your', 'listing', 'for', 'the', '2010', 'Toyota', 'Innova', 'E', 'Diesel', 'MT', 'has', 'now', 'expired.', 'To', 'renew', 'your', 'listing', 'go',),
                    array('http://www.staging4.autodeal.com.ph/account/used-cars/inquiries')
                )
            )
        );
    }

    public function getTestConstructMessagesData()
    {
        return array(
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages',
                array(
                    '2/2 http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages',
                    '1/2 Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at'
                )
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph!',
                array('Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph!')
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages',
                array(
                    '2/2 http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages',
                    '1/2 Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at'
                )
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages on AutoDeal.com.ph! View your listing at',
                array(
                    '3/3 on AutoDeal.com.ph! View your listing at',
                    '2/3 http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages',
                    '1/3 Fr AutoDeal: Great news, your listing has been approved and is now live'
                )
            )
        );
    }

    public function getTestComposeWithSmsDeliveryAddressData()
    {
        return array(
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages',
                array(
                    '2/2 http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages',
                    '1/2 Sent to: 09173149060. Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at'
                )
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph!',
                array('Sent to: 09173149060. Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph!')
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages',
                array(
                    '2/2 http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages',
                    '1/2 Sent to: 09173149060. Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at'
                )
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages on AutoDeal.com.ph! View your listing at',
                array(
                    '3/3 on AutoDeal.com.ph! View your listing at',
                    '2/3 http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages',
                    '1/3 Sent to: 09173149060. Fr AutoDeal: Great news, your listing has been approved and is now live'
                )
            )
        );
    }

    public function getTestComposeWithSmsDeliveryAddressAndLimitMessagesFalseData()
    {
        return array(
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages',
                array(
                    'Sent to: 09173149060. Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages'
                )
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph!',
                array('Sent to: 09173149060. Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph!')
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages',
                array(
                    'Sent to: 09173149060. Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages'
                )
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages on AutoDeal.com.ph! View your listing at',
                array(
                    'Sent to: 09173149060. Fr AutoDeal: Great news, your listing has been approved and is now live http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages on AutoDeal.com.ph! View your listing at'
                )
            )
        );
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/MessageComposer::splitMessage
     * @dataProvider getTestSplitMessageData
     */
    public function testSplitMessage($value, $expected)
    {
        $configurationMock = $this->getConfigurationMock();   
        $sut = new MessageComposer($configurationMock);

        $actual = $sut->splitMessage($value);
        
        $this->assertTrue(is_array($actual));
        $this->assertTrue($actual == $expected);
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/MessageComposer::constructMessages
     * @dataProvider getTestConstructMessagesData
     */
    public function testConstructMessages($value, $expected)
    {
        $message = new Message();
        $message->setFrom('AUTODEAL');
        $message->addNumber('09173149060');
        $message->setContent($value);

        $configurationMock = $this->getConfigurationMock();   
        $sut = new MessageComposer($configurationMock);

        $actual = $sut->constructMessages($message);
        
        foreach ($actual as $key => $iMessage) {
            $this->assertEquals($message->getFrom(), $iMessage->getFrom());
            $this->assertTrue($message->getNumbers() == $iMessage->getNumbers());
            $this->assertEquals($expected[$key], $iMessage->getContent());
        }
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/MessageComposer::compose
     * @dataProvider getTestComposeWithSmsDeliveryAddressData
     */
    public function testComposeWithSmsDeliveryAndLimitMessagesTrueData($value, $expected)
    {
        $message = new Message();
        $message->setFrom('AUTODEAL');
        $message->addNumber('09173149060');
        $message->setContent($value);

        $configurationMock = $this->getConfigurationMock();   
        $configurationMock->expects($this->any())
            ->method('getSmsDeliveryAddress')
            ->will($this->returnValue('09173149060'));

        $configurationMock->expects($this->any())
            ->method('isLimitMessages')
            ->will($this->returnValue(true));

        $sut = new MessageComposer($configurationMock);
        $actual = $sut->compose($message);
        
        foreach ($actual as $key => $iMessage) {
            $this->assertEquals($message->getFrom(), $iMessage->getFrom());
            $this->assertTrue($message->getNumbers() == $iMessage->getNumbers());
            $this->assertEquals($expected[$key], $iMessage->getContent());
        }
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/MessageComposer::compose
     * @dataProvider getTestComposeWithSmsDeliveryAddressAndLimitMessagesFalseData
     */
    public function testComposeWithSmsDeliveryAndLimitMessagesFalseData($value, $expected)
    {
        $message = new Message();
        $message->setFrom('AUTODEAL');
        $message->addNumber('09173149060');
        $message->setContent($value);

        $configurationMock = $this->getConfigurationMock();   
        $configurationMock->expects($this->any())
            ->method('getSmsDeliveryAddress')
            ->will($this->returnValue('09173149060'));

        $configurationMock->expects($this->any())
            ->method('isLimitMessages')
            ->will($this->returnValue(false));

        $sut = new MessageComposer($configurationMock);
        $actual = $sut->compose($message);
        
        foreach ($actual as $key => $iMessage) {
            $this->assertEquals($message->getFrom(), $iMessage->getFrom());
            $this->assertTrue($message->getNumbers() == $iMessage->getNumbers());
            $this->assertEquals($expected[$key], $iMessage->getContent());
        }
    }
}
