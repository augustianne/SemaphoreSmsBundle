<?php

/*
 * This file is part of SemaphoreSmsBundle.
 *
 * Yan Barreta <augustianne.barreta@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yan\Bundle\SemaphoreSmsBundle\Request;

use \Exception;

use \Yan\Bundle\SemaphoreSmsBundle\Request\CurlRequest;

/**
 * cUrl wrapper
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */
class Curl
{

    public function __construct()
    {
        if (!function_exists('curl_init')) {
            throw new Exception('Curl is not enabled.');
        }
    }

    public function post($url, $parameters = array())
    {
        $curlRequest = new CurlRequest($url);
        $curlRequest->setOption(CURLOPT_RETURNTRANSFER, true);
        $curlRequest->setOption(CURLOPT_HEADER, 0);
        $curlRequest->setOption(CURLOPT_VERBOSE, 0);
        $curlRequest->setOption(CURLOPT_POST, true);
        $curlRequest->setOption(CURLOPT_POSTFIELDS, $parameters);
        
        $result = $curlRequest->execute();
        
        $curlRequest->close();

        return $result;
    }

    public function get($url, $parameters = array())
    {
        $formattedUrl = sprintf("%s%s", $url, http_build_query($parameters));

        $curlRequest = new CurlRequest($url);
        $curlRequest->setOption(CURLOPT_URL, $formattedUrl);
        $curlRequest->setOption(CURLOPT_FOLLOWLOCATION, true);
        $curlRequest->setOption(CURLOPT_RETURNTRANSFER, true);
        
        $result = $curlRequest->execute();
        
        $curlRequest->close();

        return $result;
    }
}
