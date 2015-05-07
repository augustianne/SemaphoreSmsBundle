<?php

/*
 * This file is part of SemaphoreSmsBundle.
 *
 * Yan Barreta <augustianne.barreta@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Yan\Bundle\SemaphoreSmsBundle\Exception;

use \Exception;

/**
 * Unit test for SingleSmsSender
 *
 * @author  Yan Barreta
 * @version dated: April 30, 2015 3:55:29 PM
 */

class DeliveryFailureException extends Exception
{

	private $json = null;

	public function __construct($message, $json=array())
	{
		$this->json = $json;
		parent::__construct($message);
	}

	public function getJsonResult()
	{
		return $this->json;
	}

}
