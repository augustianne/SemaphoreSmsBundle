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

/**
 * SMS Message properties
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class MessageComposer
{

    const CHARACTER_LIMIT = 155;
    /**
     * Accepts a Message object, splits it into 160-character limit
     *
     * @return void
     */ 
    public function compose(Message $message)
    {
        $content = $message->getContent();
        
        return $this->constructMessages($message);
    }

    public function splitMessage($string) {
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

    public function constructMessages(Message $message) {
        
        $newMessage = $this->splitMessage($message->getContent());

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
