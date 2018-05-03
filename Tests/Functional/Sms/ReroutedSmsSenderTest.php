<?php

/*
 * This file is part of SemaphoreSmsBundle.
 *
 * Yan Barreta <augustianne.barreta@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yan\Bundle\SemaphoreSmsBundle\Tests\Functional\Sms;

use Yan\Bundle\SemaphoreSmsBundle\Sms\Message;
use Yan\Bundle\SemaphoreSmsBundle\Tests\Fixtures\AppKernel;

use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Scope;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Unit test for SingleSmsSender
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class ReroutedSmsSenderTest extends \PHPUnit_Framework_TestCase
{
    private $container;
    private $kernel;

    public function setUp()
    {
        $this->kernel = new AppKernel('semaphore_reroute', true);
        $this->kernel->boot();
        
        $this->container = $this->kernel->getContainer();

        $fixturesDir = __DIR__.'/../../Fixtures';
        $this->fs = new FileSystem();
        $this->fs->remove(array($fixturesDir.'/cache', $fixturesDir.'/logs'));
    }

    public function getMessageDefaultSenderData()
    {
        return array(
            array(array('09173149060'), 'Test message 1.'),
            array(array('09173149060', '09173149060', '09173222385'), 'Test message 2.'),
            array(array('09173149060', '09173222385'), 'Test message 3.'),
            array(array('09173149060', '09173149060'), 'Test message 4.')
        );
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/RegularSmsSender::send
     * @dataProvider getMessageDefaultSenderData
     */
    public function testSendingRegularSmsDefaultSender($numbers, $content)
    {
        $sms = new Message();
        $sms->setContent('Regular: '.$content);

        foreach ($numbers as $number) {
            $sms->addNumber($number);
        }

        $smsSender = $this->container->get('yan.semaphore_sms.regular_sms_sender');

        $sent = $smsSender->send($sms);
        $this->assertTrue($sent, 'Message was not sent');
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/PrioritySmsSender::send
     * @dataProvider getMessageDefaultSenderData
     */
    public function testSendingPrioritySmsDefaultSender($numbers, $content)
    {
        $sms = new Message();
        $sms->setContent('Priority: '.$content);

        foreach ($numbers as $number) {
            $sms->addNumber($number);
        }

        $smsSender = $this->container->get('yan.semaphore_sms.priority_sms_sender');

        $sent = $smsSender->send($sms);
        $this->assertTrue($sent, 'Message was not sent');
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/BulkSmsSender::send
     * @dataProvider getMessageDefaultSenderData
     */
    public function testSendingBulkSmsDefaultSender($numbers, $content)
    {
        $sms = new Message();
        $sms->setContent('Bulk: '.$content);

        foreach ($numbers as $number) {
            $sms->addNumber($number);
        }

        $smsSender = $this->container->get('yan.semaphore_sms.bulk_sms_sender');

        $sent = $smsSender->send($sms);
        $this->assertTrue($sent, 'Message was not sent');
    }

    /**
     * @covers Yan/Bundle/SemaphoreSmsBundle/Sms/SingleSmsSender::send
     * @dataProvider getMessageDefaultSenderData
     */
    public function testSendingSingleSmsDefaultSender($numbers, $content)
    {
        $sms = new Message();
        $sms->setContent('Single: '.$content);

        foreach ($numbers as $number) {
            $sms->addNumber($number);
        }

        $smsSender = $this->container->get('yan.semaphore_sms.single_sms_sender');

        $sent = $smsSender->send($sms);
        $this->assertTrue($sent, 'Message was not sent');
    }
}
