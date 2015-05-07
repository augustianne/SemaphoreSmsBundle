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
class CurlRequest
{

    private $handle = null;

    public function __construct($url)
    {
        if (!function_exists('curl_init')) {
            throw new Exception('Curl is not enabled.');
        }

        $this->handle = curl_init($url);
    }

    public function setOption($name, $value)
    {
        curl_setopt($this->handle, $name, $value);
    }

    public function execute()
    {
        return curl_exec($this->handle);
    }

    public function getInfo($name)
    {
        return curl_getinfo($this->handle, $name);
    }

    public function close()
    {
        curl_close($this->handle);
    }
}
