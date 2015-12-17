<?php

/**
 * The system exception can be used for showing a system error like a missing file or a broken connection.
 */
class RestSystemException extends Exception
{

    public function __construct($message, $errorCode)
    {
        parent::__construct($message, $errorCode);
    }
}
