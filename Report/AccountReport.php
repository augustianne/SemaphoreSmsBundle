<?php

/*
 * This file is part of SemaphoreSmsBundle.
 *
 * Yan Barreta <augustianne.barreta@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yan\Bundle\SemaphoreSmsBundle\Report;

use \InvalidArgumentException;

use Yan\Bundle\SemaphoreSmsBundle\Exception\DeliveryFailureException;
use Yan\Bundle\SemaphoreSmsBundle\Request\Curl;
use Yan\Bundle\SemaphoreSmsBundle\Sms\SemaphoreSmsConfiguration;

/**
 * Bulk SMS
 *
 * @author  Yan Barreta
 * @version dated: December 15, 2016 5:09:55 PM
 */
class AccountReport
{

    protected $config;
    protected $curl;
    
    public function __construct(SemaphoreSmsConfiguration $config, Curl $curl)
    {
        $this->config = $config;
        $this->curl = $curl;
    }

    /**
     * Retrieves url for api reporting
     *
     * @return void
     */ 
    public function getUrl()
    {
        return 'https://api.semaphore.co/api/v4/account';
    }

    /**
     * Composes single text message for sending
     *
     * @param void
     * @return Array
     */ 
    public function composeParameters()
    {
        $params = array(
            'apikey' => $this->config->getApiKey()
        );

        return $params;
    }

    /**
     * Retrieves sms credits
     *
     * @return int
     * @throws Exception
     */ 
    public function getAccountBalance()
    {
        $result = $this->curl->get(
            $this->getUrl(), 
            $this->composeParameters()
        );
        
        $json = json_decode($result, true);
        
        if (!is_array($json)) {
            throw new DeliveryFailureException('Request sending failed.');
        }

        return isset($json['credit_balance']) ? $json['credit_balance'] : false;
    }

    /**
     * Retrieves account status
     *
     * @return int
     * @throws Exception
     */ 
    public function getAccountStatus()
    {
        $result = $this->curl->get(
            $this->getUrl(), 
            $this->composeParameters()
        );

        $json = json_decode($result, true);

        if (!is_array($json)) {
            throw new DeliveryFailureException('Request sending failed.');
        }

        return isset($json['status']) ? $json['status'] : false;
    }
}
