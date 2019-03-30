<?php

namespace App\Exceptions;

use Exception;

/**
 *
 * Class ApiHandler
 * @package App\Exceptions
 */
class ApiHandler extends Exception
{
    function __construct($msg = '',$code = 422)
    {
        parent::__construct($msg, $code);
    }
}
