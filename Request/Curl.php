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
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public function get($url, $parameters = array())
    {
        $formattedUrl = sprintf("%s%s", $url, http_build_query($parameters));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $formattedUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}
