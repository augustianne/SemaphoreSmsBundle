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

use \InvalidArgumentException;

use Yan\Bundle\SemaphoreSmsBundle\Request\Curl;
use Yan\Bundle\SemaphoreSmsBundle\Sms\Message;
use Yan\Bundle\SemaphoreSmsBundle\Sms\SmsSender;

/**
 * Single SMS
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class SingleSmsSender extends SmsSender
{

    /**
     * Retrieves url for sms sending
     *
     * @return void
     */ 
    public function initUrl()
    {
        return 'http://api.semaphore.co/api/sms';
    }

    /**
     * Composes single text message for sending
     *
     * @param Message $message
     * @throws \InvalidArgumentException
     * @return Array
     */ 
    public function composeParameters(Message $message)
    {
        $numbers = $message->getNumbers();
        if (count($numbers) > 1) {
            throw new InvalidArgumentException('Multiple number is not allowed. Use Bulk Sms Sender instead.');
        }
        
        $smsDeliveryAddress = $this->config->getSmsDeliveryAddress();
        
        $formattedNumbers = $message->formatNumber();
        if (!is_null($smsDeliveryAddress)) {
            $formattedNumbers = $smsDeliveryAddress;
        }
        
        $params = array(
            'api' => $this->config->getApiKey(),
            'number' => $formattedNumbers,
            'message' => $message->getContent(),
            'from' => $this->getSender($message)
        );

        return $params;
    }
}
