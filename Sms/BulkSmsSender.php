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
use Yan\Bundle\SemaphoreSmsBundle\Sms\SmsSender;

/**
 * Bulk SMS
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class BulkSmsSender extends SmsSender
{

    private $url;

    /**
     * Sets url for sms sending
     *
     * @return void
     */ 
    public function initUrl()
    {
        $this->url = 'http://www.semaphore.co/v3/bulk_api/sms';
    }
}