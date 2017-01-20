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

use Yan\Bundle\SemaphoreSmsBundle\Sms\Message;
use Yan\Bundle\SemaphoreSmsBundle\Sms\SemaphoreSmsConfiguration;

/**
 * SMS Message properties
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class MessageComposer
{

    const CHARACTER_LIMIT = 155;
    
    protected $config;

    public function __construct(SemaphoreSmsConfiguration $config)
    {
        $this->config = $config;
    }

    /**
     * Accepts a Message object, splits it into Messages 
     * that has content under 155 characters
     * 
     * @param Message
     * @return Array of Messages
     */ 
    public function compose(Message $message)
    {
        $smsDeliveryAddress = $this->config->getSmsDeliveryAddress();

        if (!is_null($smsDeliveryAddress)) {
            $formattedMessage = sprintf('Sent to: %s. %s', $message->formatNumber(), $message->getContent());

            $message->setContent($formattedMessage);
            $message->setNumbers(explode(',', str_replace(' ', '', $smsDeliveryAddress)));
        }

        $messages = array($message);

        if (!$this->config->isLimitMessages()) {
            return $messages;    
        }    
        
        return $this->constructMessages($message);
    }

    /**
     * Splits string into 155-character substrings
     *
     * @param string
     * @return Array
     */ 
    public function splitMessage($string) 
    {
        $words = explode(' ', $string);
        
        $newMessage = array();
        $temp = array();
        for ($i = 0; $i < count($words); $i++) {
            $word = $words[$i];

            $temp[] = $word;
            $tempString = implode(' ', $temp);
            $tempStringLength = strlen($tempString);
            
            if ($tempStringLength > self::CHARACTER_LIMIT) {
                $theword = array_pop($temp);
                $newMessage[] = $temp;
                
                if (strlen($theword) <= self::CHARACTER_LIMIT) {
                    $temp = array();
                    $i--;
                }
                else {
                    $temp = array($theword);
                }
            }
        }

        $newMessage[] = $temp;

        return $newMessage;
    }

    /**
     * Accepts a Message object, splits it into Messages 
     * that has content under 155 characters
     * 
     * @param Message
     * @return Array of Messages
     */ 
    public function constructMessages(Message $message) 
    {   
        $content = $message->getContent();
        $newMessage = $this->splitMessage($content);

        $parts = count($newMessage);
        $messages = array();

        foreach ($newMessage as $key => $iNewMessage) {
            if ($parts > 1) {
                $part = ($key+1);
                array_unshift($iNewMessage, "$part/$parts");
            }

            $clonedMessage = clone ($message);
            $clonedMessage->setContent(implode(' ', $iNewMessage));
            
            $messages[] = $clonedMessage;
        }

        return array_reverse($messages);
    }

}
