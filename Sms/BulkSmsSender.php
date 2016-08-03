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
 * Bulk SMS
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class BulkSmsSender extends SmsSender
{

    /**
     * Retrieves url for sms sending
     *
     * @return void
     */ 
    public function initUrl()
    {
        return 'http://www.semaphore.co/v3/bulk_api/sms';
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
        $smsDeliveryAddress = $this->config->getSmsDeliveryAddress();
        
        $formattedNumbers = $message->formatNumber();
        $formattedMessage = $message->getContent();
        if (!is_null($smsDeliveryAddress)) {
            $formattedNumbers = $smsDeliveryAddress;
            $formattedMessage = sprintf('Sent to: %s. %s', $message->formatNumber(), $message->getContent());
        }

        $params = array(
            'api' => $this->config->getApiKey(),
            'number' => $formattedNumbers,
            'message' => $formattedMessage,
            'from' => $this->getSender($message)
        );

        return $params;
    }
}
