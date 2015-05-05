<?php

/*
 * This file is part of SemapahoreSmsBundle.
 *
 * Yan Barreta <augustianne.barreta@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yan\Bundle\SemapahoreSmsBundle\Sms;

/**
* Class for bundle configuration
*
* @author  Yan Barreta
* @version dated: Apr 30, 2015 2:27:58 PM
*/
class SemaphoreSmsConfiguration
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Retrieves api_key
     *
     * @param void
     * @return boolean
     */
    public function getApiKey()
    {
        return $this->container->getParameter('semaphore_sms.api_key');
    }

    /**
     * Retrieves sender_name
     *
     * @param void
     * @return String
     */
    public function getSenderName()
    {
        return $this->container->getParameter('semaphore_sms.sender_name');
    }
          
}