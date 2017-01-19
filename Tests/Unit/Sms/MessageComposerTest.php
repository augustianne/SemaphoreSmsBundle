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
    private $sut;
    
    protected function setUp()
    {
        $this->sut = new MessageComposer();
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
            )
        );
    }

    public function getTestConstructMessagesData()
    {
        return array(
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages',
                array(
                    '1/2 Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at', 
                    '2/2 http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages'
                )
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph!',
                array('Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph!')
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages',
                array(
                    '1/2 Fr AutoDeal: Great news, your listing has been approved and is now live on AutoDeal.com.ph! View your listing at', 
                    '2/2 http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages'
                )
            ),
            array(
                'Fr AutoDeal: Great news, your listing has been approved and is now live http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages on AutoDeal.com.ph! View your listing at',
                array(
                    '1/3 Fr AutoDeal: Great news, your listing has been approved and is now live', 
                    '2/3 http://www.staging3.autotaging3.autodeal.com.ph/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages/app_dev.php/account/messages',
                    '3/3 on AutoDeal.com.ph! View your listing at'
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
        $actual = $this->sut->splitMessage($value);
        
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

        $actual = $this->sut->constructMessages($message);
        foreach ($actual as $key => $iMessage) {
            $this->assertEquals($message->getFrom(), $iMessage->getFrom());
            $this->assertTrue($message->getNumbers() == $iMessage->getNumbers());
            $this->assertEquals($expected[$key], $iMessage->getContent());
        }
    }
}
