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

use Yan\Bundle\SemaphoreSmsBundle\Request\Curl;
use Yan\Bundle\SemaphoreSmsBundle\Sms\Message;

/**
 * Actual sending of sms
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class SmsSender
{

    private $config;
    private $curl;
    private $url;
    
    public function __construct(SemaphoreConfiguration $config, Curl $curl)
    {
        $this->config = $config;
        $this->curl = $curl;

        $this->initUrl();
    }

    public function getUrl()
    {
        return $this->urll
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
        $from = $message->getFrom();
        $from = empty($from) ? $this->config->getSenderName() : $from;

        $params = array(
            'api' => $this->config->getApiKey(),
            'number' => $message->formatNumber(),
            'message' => $message->getMessage(),
            'from' => $from
        );

        $this->curl->post($this->getUrl(), $params);
    }
}
