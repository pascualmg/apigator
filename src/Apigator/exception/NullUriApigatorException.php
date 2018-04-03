<?php
/**
 * Created by PhpStorm.
 * User: Pascual
 * Date: 30/03/2018
 * Time: 21:44
 */

namespace Apigator\Exception;


use Throwable;

class NullUriApigatorException extends \Exception
{
    public function __construct($message = "we need a URI!", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}