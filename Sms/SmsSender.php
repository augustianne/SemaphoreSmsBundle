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
    
    public function __construct(SemaphoreSmsConfiguration $config, Curl $curl)
    {
        $this->config = $config;
        $this->curl = $curl;
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
        $result = $this->curl->post(
            $this->getUrl(), 
            $this->composeParameters($message)
        );

        $json = json_decode($result, true);

        if (!is_array($json)) {
            throw new DeliveryFailureException('Request sending failed.');
        }

        if ($json['status'] != 'success') {
            $message = isset($json['message']) ? $json['message'] : 'Delivery Failure';
            
            throw new DeliveryFailureException($message, $json);
        }
        else {
            return true;
        }
    }
}
