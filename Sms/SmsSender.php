<?php

/*
 * This file is part of SemaphoreSmsBundle.
 *
 * Yan Barreta <augustianne.barreta@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yan\Bundle\SemaphoreSmsBundle\Sms;

use Yan\Bundle\SemaphoreSmsBundle\Exception\DeliveryFailureException;
use Yan\Bundle\SemaphoreSmsBundle\Request\Curl;
use Yan\Bundle\SemaphoreSmsBundle\Sms\SemaphoreSmsConfiguration;

/**
 * Actual sending of sms
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
abstract class SmsSender
{

    protected $config;
    protected $curl;
    protected $messageComposer;
    
    public function __construct(SemaphoreSmsConfiguration $config, Curl $curl, MessageComposer $messageComposer)
    {
        $this->config = $config;
        $this->curl = $curl;
        $this->messageComposer = $messageComposer;
    }

    abstract public function initUrl();
    abstract public function composeParameters(Message $message);

    public function getUrl()
    {
        return $this->initUrl();
    }

    public function getSender(Message $message)
    {
        $from = $message->getFrom();
        $from = empty($from) ? $this->config->getSenderName() : $from;

        return $from;
    }

    /**
     * Sends actual text messages
     *
     * @param Message $message
     * @return void
     * @throws Exception
     */ 
    public function send(Message $message)
    {
        $messages = $this->messageComposer->compose($message);

        foreach ($messages as $iMessage) {
            $result = $this->curl->post(
                $this->getUrl(), 
                $this->composeParameters($iMessage)
            );

            $json = json_decode($result, true);
            
            if (!is_array($json)) {
                throw new DeliveryFailureException('Request sending failed.');
            }
        }

        return true;
    }
}
