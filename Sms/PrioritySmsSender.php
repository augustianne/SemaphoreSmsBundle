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
 * Priority SMS
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class PrioritySmsSender extends SmsSender
{

    /**
     * Retrieves url for sms sending
     *
     * @return void
     */ 
    public function initUrl()
    {
        return 'http://api.semaphore.co/api/v4/priority';
    }
}
